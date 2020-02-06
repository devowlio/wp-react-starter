import { applyDefaultRunnerConfiguration } from "../../../common/Gruntfile";

function setupGrunt(grunt: IGrunt) {
    // Project configuration (the base path is set to the projects root, so ../ level up)
    grunt.initConfig({});

    // Load WP ReactJS Starter initial tasks
    applyDefaultRunnerConfiguration(grunt);
}

// See https://github.com/samuelneff/grunt-script-template/blob/2864d3cb1cf424d9ab83fdd2ed5a6c24917cf19b/Gruntfile.ts#L54
export = setupGrunt;
