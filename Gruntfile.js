"use strict";

module.exports = function(grunt) {
    // Project configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        clean: {
            /**
             * Task to clean the already copied node modules to the public library folder
             */
            npmLibs: [] // Your library folders
        },
        copy: {
            /**
             * Task to copy npm modules to the public library folder.
             */
            npmLibs: {
                expand: true,
                cwd: 'node_modules',
                src: [], // Your library files
                dest: 'public/lib/'
            }
        }
    });
    
    // Load WP ReactJS Starter tasks (do not remove this and initConfig should be called until here already)
    require('./build/grunt.js')(grunt);
    
    // Load npm tasks
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
    
    // Register copy lib task
    grunt.registerTask('copy-npmLibs', ['clean:npmLibs', 'copy:npmLibs', 'node_modules_cachebuster:publiclib']);
    
    // Register default task
    grunt.registerTask('default', function() {
        grunt.log.write("Your default task...");
    });
};