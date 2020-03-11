/* eslint-disable @typescript-eslint/no-var-requires */
/* eslint-disable import/no-extraneous-dependencies */
/**
 * Common rules / configuration for WordPress webpack builder.
 *
 * To make webpack possible with *.ts "ts-node" is needed for the "interpret" package.
 */

import { resolve, join } from "path";
import fs from "fs";
import { Configuration, DefinePlugin, Compiler, Options } from "webpack";
import { spawn, execSync } from "child_process";
import WebpackBar from "webpackbar";
import MiniCssExtractPlugin from "mini-css-extract-plugin";
import ForkTsCheckerWebpackPlugin from "fork-ts-checker-webpack-plugin";

/**
 * An internal plugin which runs build:webpack:done script.
 */
class WebpackPluginDone {
    private pwd: string;

    constructor(pwd: string) {
        this.pwd = pwd;
    }

    public apply(compiler: Compiler) {
        const env = Object.create(process.env);
        env.TS_NODE_PROJECT = resolve(this.pwd, "tsconfig.json");

        compiler.plugin("done", () => {
            spawn("yarn --silent build:webpack:done", {
                stdio: "inherit",
                shell: true,
                cwd: this.pwd,
                env
            }).on("error", (err) => console.log(err));
        });
    }
}

/**
 * Convert a slug like "my-plugin" to "myPlugin". This can
 * be useful for library naming (window[""] is bad because the hyphens).
 *
 * @param slug
 * @returns
 */
function slugCamelCase(slug: string) {
    return slug.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
}

/**
 * Generate externals because for add-on development we never bundle the dependent to the add-on!
 *
 * @returns {object}
 */
function getPlugins(pwd: string) {
    const lernaList: LernaListItem[] = JSON.parse(
        execSync("yarn --silent lerna list --loglevel silent --json --all").toString()
    );

    // Filter only plugins, not packages
    const pluginList = lernaList.filter(({ location }) => location.startsWith(resolve(pwd, "../..", "plugins")));

    // We determine the externals due the entry points
    const tsFolder = "src/public/ts";
    const plugins: Array<LernaListItem & {
        modulePath: string;
        moduleId: string;
        externalId: string;
        entrypointName: string;
    }> = [];

    pluginList.forEach((item) =>
        fs
            .readdirSync(join(item.location, tsFolder), { withFileTypes: true })
            .filter((f) => !f.isDirectory())
            // Now we have all entry points available
            .forEach((f) => {
                const [entrypointName] = f.name.split(".", 1);
                const modulePath = resolve(item.location, tsFolder, f.name);
                const moduleId = `${item.name}/${tsFolder}/${entrypointName}`;
                const externalId = `${slugCamelCase(
                    require(resolve(item.location, "package.json")).slug
                )}_${entrypointName}`; // see output.library

                plugins.push({ ...item, modulePath, moduleId, externalId, entrypointName });
            })
    );
    return plugins;
}

/**
 * Generate externals because for add-on development we never bundle the dependent to the add-on!
 *
 * @returns {object}
 */
function getPackages(pwd: string, rootSlugCamelCased: string) {
    const lernaList: LernaListItem[] = JSON.parse(
        execSync("yarn --silent lerna list --loglevel silent --json --all").toString()
    );

    // Filter only packages, not plugins
    const packageList = lernaList.filter(({ location }) => location.startsWith(resolve(pwd, "../..", "packages")));

    // We determine the externals due the entry points
    const packages: Array<LernaListItem & {
        externalId: string;
    }> = [];

    packageList.forEach((item) => {
        packages.push({ ...item, externalId: `${rootSlugCamelCased}_${item.name.split("/")[1]}` });
    });
    return packages;
}

/**
 * The result of yarn lerna list as JSON.
 */
type LernaListItem = {
    name: string;
    version: string;
    private: boolean;
    location: string;
};

type FactoryValues = {
    pwd: string;
    mode: string;
    rootPkg: any;
    pkg: any;
    plugins: ReturnType<typeof getPlugins>;
    slug: string;
    type: "plugin" | "package";
    rootSlugCamelCased: string;
    slugCamelCased: string;
    outputPath: string;
    tsFolder: string;
};

/**
 * Create default settings for the current working directory.
 * If you want to do some customizations you can pass an override function
 * which passes the complete default settings and you can mutate it.
 */
function createDefaultSettings(
    type: "plugin" | "package",
    {
        override,
        definePlugin = (processEnv) => processEnv,
        webpackBarOptions = (options) => options
    }: {
        override?: (settings: Configuration[], factoryValues: FactoryValues) => void;
        definePlugin?: (processEnv: any) => any;
        webpackBarOptions?: (options: WebpackBar.Options) => WebpackBar.Options;
    } = {}
) {
    const pwd = process.env.DOCKER_START_PWD || process.env.PWD;
    const NODE_ENV = (process.env.NODE_ENV as Configuration["mode"]) || "development";
    const nodeEnvFolder = NODE_ENV === "production" ? "dist" : "dev";
    const rootPkg = require(resolve(pwd, "../../package.json"));
    const pkg = require(resolve(pwd, "package.json"));
    const settings: Configuration[] = [];
    const plugins = getPlugins(pwd);
    const slug = type === "plugin" ? pkg.slug : pkg.name.split("/")[1];

    const rootSlugCamelCased = slugCamelCase(rootPkg.name);
    const packages = getPackages(pwd, rootSlugCamelCased);
    const slugCamelCased = slugCamelCase(slug);
    const outputPath = type === "plugin" ? join(pwd, "src/public", nodeEnvFolder) : join(pwd, nodeEnvFolder);
    const tsFolder = resolve(pwd, type === "plugin" ? "src/public/ts" : "lib");

    const pluginSettings: Configuration = {
        context: pwd,
        mode: NODE_ENV,
        optimization: {
            usedExports: true,
            sideEffects: true,
            splitChunks: {
                cacheGroups:
                    type === "plugin"
                        ? {
                              // Dynamically get the entries from first-level files so every entrypoint get's an own vendor file (https://git.io/Jv2XY)
                              ...plugins
                                  .filter(({ location }) => location === pwd)
                                  .reduce((map: { [key: string]: Options.CacheGroupsOptions }, obj) => {
                                      map[`vendor~${obj.entrypointName}`] = {
                                          test: /node_modules.*(?<!\.css)(?<!\.scss)(?<!\.less)$/,
                                          chunks: (chunk) => chunk.name === obj.entrypointName,
                                          name: `vendor~${obj.entrypointName}`,
                                          enforce: true
                                      };
                                      return map;
                                  }, {})
                          }
                        : {
                              vendor: {
                                  test: /node_modules.*(?<!\.css)(?<!\.scss)(?<!\.less)$/,
                                  chunks: "initial",
                                  enforce: true
                              }
                          }
            }
        },
        output: {
            path: outputPath,
            filename: "[name].js",
            library: `${type === "plugin" ? slugCamelCased : rootSlugCamelCased}_${
                type === "plugin" ? "[name]" : slugCamelCased
            }`
        },
        entry:
            type === "plugin"
                ? {
                      // Dynamically get the entries from first-level files
                      ...plugins
                          .filter(({ location }) => location === pwd)
                          .reduce((map: { [key: string]: string }, obj) => {
                              map[obj.entrypointName] = obj.modulePath;
                              return map;
                          }, {})
                  }
                : {
                      index: resolve(pwd, "lib/index.tsx")
                  },
        externals: {
            react: "React",
            "react-dom": "ReactDOM",
            jquery: "jQuery",
            mobx: "mobx",
            wp: "wp",
            _: "_",
            lodash: "lodash",
            wpApiSettings: "wpApiSettings",
            "@wordpress/i18n": "wp['i18n']",
            // @wordpress/i18n relies on moment, but it also includes moment-timezone which also comes with WP core
            moment: "moment",
            "moment-timezone": "moment",
            // Get dynamically a map of externals for add-on development
            ...plugins.reduce((map: { [key: string]: string }, obj) => {
                map[obj.externalId] = obj.moduleId;
                return map;
            }, {}),
            // Get dynamically a map of external package dependencies
            ...packages.reduce((map: { [key: string]: string }, obj) => {
                map[obj.name] = obj.externalId;
                return map;
            }, {})
        },
        devtool: "#source-map",
        module: {
            rules: [
                {
                    test: /\.tsx$/,
                    include: tsFolder,
                    use: [
                        "cache-loader",
                        "thread-loader",
                        {
                            loader: "babel-loader?cacheDirectory",
                            options: pkg.babel
                        }
                    ]
                },
                {
                    test: /\.(scss|css)$/,
                    include: tsFolder,
                    use: [
                        MiniCssExtractPlugin.loader,
                        "css-loader?url=false",
                        {
                            loader: "postcss-loader",
                            options: {
                                plugins: [
                                    require("autoprefixer")({}),
                                    require(resolve(pwd, "../../common/postcss-plugin-clean"))({})
                                ]
                            }
                        },
                        "sass-loader"
                    ]
                }
            ]
        },
        resolve: {
            extensions: [".js", ".jsx", ".ts", ".tsx"]
        },
        plugins: [
            new WebpackBar(
                webpackBarOptions({
                    name: slug
                })
            ),
            new ForkTsCheckerWebpackPlugin(),
            new DefinePlugin({
                // NODE_ENV is used inside React to enable/disable features that should only be used in development
                "process.env": definePlugin({
                    NODE_ENV: JSON.stringify(NODE_ENV),
                    env: JSON.stringify(NODE_ENV),
                    slug: JSON.stringify(slug)
                })
            }),
            new MiniCssExtractPlugin({
                filename: "[name].css"
            }),
            new WebpackPluginDone(pwd)
        ]
    };

    settings.push(pluginSettings);

    override?.(settings, {
        pwd,
        mode: NODE_ENV,
        rootPkg,
        pkg,
        plugins,
        slug,
        type,
        rootSlugCamelCased,
        slugCamelCased,
        outputPath,
        tsFolder
    });

    return settings;
}

export { createDefaultSettings, slugCamelCase, getPlugins };
