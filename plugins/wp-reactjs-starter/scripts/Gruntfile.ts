import { applyPluginRunnerConfiguration } from "../../../common/Gruntfile.plugin";

function setupGrunt(grunt: IGrunt) {
    // Project configuration (the base path is set to the projects root, so ../ level up)
    grunt.initConfig({
        BUILD_POST_TASKS: ["compress:installablePlugin"], // This grunt tasks runs after the build is complete (some are defined in build/Gruntfile.base.js)
        clean: {
            /**
             * Task to clean the already copied node modules to the public library folder.
             * This is needed for the libs:copy task.
             */
            npmLibs: {
                expand: true,
                cwd: "src/public/lib/",
                src: ["react", "react-dom", "mobx"]
            },
            /**
             * Task to clean sourcemap ".map" and non-minified files from libraries which are not needed
             * on production build. This is useful for example to reduce the size of the plugin
             * itself because some hosting providers only allow an upload size of 2 MB if it
             * is sold outside of wordpress.org. See also strip_code:sourcemaps.
             */
            productionLibs: {
                expand: true,
                cwd: "<%= BUILD_PLUGIN_DIR %>/public/lib",
                src: [
                    "mobx/lib/mobx.umd.js",
                    "react/umd/react.development.js",
                    "react-dom/umd/react-dom.development.js"
                ]
            }
        },
        strip_code: /* eslint-disable-line @typescript-eslint/naming-convention */ {
            /**
             * With clean:productionLibs all sourcemap files are cleaned. To avoid 404 errors
             * on client side you also need to remove the link to the sourcemap.
             */
            sourcemaps: {
                options: {
                    patterns: /^\/{2}#\s*sourceMappingURL=.*\.map\s*$/gim
                },
                expand: true,
                src: ["<%= BUILD_PLUGIN_DIR %>/public/lib/mobx/lib/mobx.umd.min.js"]
            }
        },
        copy: {
            /**
             * Task to copy npm modules to the public library folder. This are mostly libraries you
             * enqueue in your Assets.php file and is added as "external" in your webpack config.
             */
            npmLibs: {
                expand: true,
                cwd: "node_modules",
                src: [
                    "react/umd/react.?(development|production.min).js",
                    "react-dom/umd/react-dom.?(development|production.min).js",
                    "mobx/lib/mobx.umd*.js",
                    "?(react|react-dom|mobx)/LICENSE*"
                ],
                dest: "src/public/lib/"
            }
        }
    });

    // Load WP ReactJS Starter initial tasks
    applyPluginRunnerConfiguration(grunt);
}

// See https://github.com/samuelneff/grunt-script-template/blob/2864d3cb1cf424d9ab83fdd2ed5a6c24917cf19b/Gruntfile.ts#L54
export = setupGrunt;
