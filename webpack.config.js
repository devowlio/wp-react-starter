var path = require("path"),
    webpack = require("webpack"),
    exec = require("child_process").exec,
    NODE_ENV = process.env.NODE_ENV || "development",
    MiniCssExtractPlugin = require("mini-css-extract-plugin"),
    dist = path.join(__dirname, "public", NODE_ENV === "production" ? "dist" : "dev"),
    WebpackBar = require("webpackbar");

module.exports = {
    mode: NODE_ENV,
    entry: {
        widget: "./public/src/widget.tsx",
        admin: "./public/src/admin.tsx"
    },
    output: {
        path: dist,
        filename: "[name].js"
    },
    externals: {
        react: "React",
        "react-dom": "ReactDOM",
        jquery: "jQuery",
        mobx: "mobx",
        "mobx-state-tree": "mobxStateTree",
        moment: "moment",
        "moment-timezone": "moment",
        wp: "wp",
        _: "_",
        wpApiSettings: "wpApiSettings",
        "@wordpress/i18n": "wp['i18n']"
    },
    devtool: "#source-map",
    module: {
        rules: [
            {
                test: /\.tsx$/,
                exclude: /(disposables)/,
                use: "babel-loader?cacheDirectory"
            },
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    {
                        loader: "postcss-loader",
                        options: {
                            config: {
                                ctx: {
                                    clean: {}
                                }
                            }
                        }
                    },
                    "sass-loader"
                ]
            }
        ]
    },
    resolve: {
        extensions: [".js", ".jsx", ".ts", ".tsx"],
        modules: ["node_modules", "public/src"]
    },
    plugins: [
        new WebpackBar(),
        new webpack.DefinePlugin({
            // NODE_ENV is used inside React to enable/disable features that should only be used in development
            "process.env": {
                NODE_ENV: JSON.stringify(NODE_ENV),
                env: JSON.stringify(NODE_ENV)
            }
        }),
        new MiniCssExtractPlugin("[name].css"),
        new ((function() {
            // Short plugin to run script on build finished to recreate the cachebuster
            function WebPackRecreateCachebuster() {}
            WebPackRecreateCachebuster.prototype.apply = function(compiler) {
                compiler.plugin("done", function(compilation, callback) {
                    setTimeout(function() {
                        console.log("Running webpack-build-done script...");
                    }, 0);
                    exec("npm run webpack-build-done").stdout.pipe(process.stdout);
                });
            };
            return WebPackRecreateCachebuster;
        })())()
    ]
};
