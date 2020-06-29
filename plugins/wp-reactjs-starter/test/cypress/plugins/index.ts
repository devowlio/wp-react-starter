// ***********************************************************
// This example plugins/index.js can be used to load plugins
//
// You can change the location of this file or turn off loading
// the plugins file with the 'pluginsFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/plugins-guide
// ***********************************************************

// This function is called when a project is opened or re-opened (e.g. due to
// the project's config changing)

import { execSync } from "child_process";
import cypressWebpackPreprocessor from "@cypress/webpack-preprocessor";
import { resolve } from "path";
import retryPlugin from "cypress-plugin-retries/lib/plugin";

// eslint-disable-next-line import/no-extraneous-dependencies
require("dotenv").config({
    path: resolve(process.env.PWD, "../../.env")
});

/**
 * As we can not generate a dynamic cypress.json we do this through a plugin.
 *
 * @see https://docs.cypress.io/guides/tooling/plugins-guide.html#Configuration
 * @see https://docs.cypress.io/api/plugins/configuration-api.html#Usage
 */
function applyConfig(config) {
    const isCI = process.env.CI;

    config.screenshotsFolder = "test/cypress/screenshots";
    config.videosFolder = "test/cypress/videos";
    config.supportFile = "test/cypress/support/index.ts";
    config.fixturesFolder = "test/cypress/fixtures";
    config.ignoreTestFiles = "*.ts";
    config.env.RETRIES = 3;
    // config.testFiles = "test/cypress/integration/**/*.{feature,features}";

    // The both configs can not be altered through this plugin because they are needed at CLI startup
    // config.integrationFolder;
    // config.pluginsFile;

    if (isCI) {
        // CI relevant options
        config.baseUrl = "http://wordpress";

        // Currently, cypress does not support multiple reporters out-of-the-box and if
        // junit report is enabled, the console does not longer output error messages locally.
        config.reporter = "junit";
        config.reporterOptions = {
            mochaFile: "test/junit/cypress.xml"
        };
    } else {
        // Local development
        const [wpContainer] = JSON.parse(
            execSync("docker container inspect $(yarn --silent root:run docker-compose:name-wordpress)").toString()
        );
        const definedPort = wpContainer.HostConfig.PortBindings["80/tcp"][0].HostPort;
        config.baseUrl = process.env.WP_LOCAL_INSTALL_URL || `http://localhost:${definedPort}`;
    }
}

module.exports = (on, config) => {
    applyConfig(config);
    retryPlugin(on);

    // `on` is used to hook into various events Cypress emits
    // `config` is the resolved Cypress config

    /**
     * TypeScript integration, see also https://docs.cypress.io/guides/tooling/typescript-support.html#Transpiling-TypeScript-test-files
     */
    on(
        "file:preprocessor",
        cypressWebpackPreprocessor({
            webpackOptions: {
                mode: "development",
                resolve: {
                    extensions: [".ts", ".js"]
                },
                // @see https://git.io/JeAtF
                // eslint-disable-next-line @typescript-eslint/naming-convention
                node: { fs: "empty", child_process: "empty", readline: "empty" },
                module: {
                    rules: [
                        {
                            test: /\.ts$/,
                            exclude: /(disposables)/,
                            use: {
                                loader: "babel-loader",
                                options: require(resolve(process.env.PWD, "package.json")).babel
                            }
                        },
                        {
                            test: /\.feature$/,
                            use: "cypress-cucumber-preprocessor/loader"
                        },
                        {
                            test: /\.features$/,
                            use: "cypress-cucumber-preprocessor/lib/featuresLoader"
                        }
                    ]
                }
            }
        })
    );

    console.log("Check if docker container is reachable...");
    execSync(`yarn --silent root:run wp-cli "wp core version"`);
    console.log("Reachable");

    return config;
};
