"use strict";

module.exports = function(grunt) {
    // Project configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'), // DO NOT REMOVE
        SERVE_DIR: 'dist', // The serve folder
        SERVE_POST_TASKS: [], // This grunt tasks runs after the serve is complete
        clean: {
            /**
             * DO NOT REMOVE. Task to clean the already copied node modules to the public library folder
             */
            npmLibs: [
                'public/lib/react/',
                'public/lib/react-dom/'
            ] // Your library folders, do not use 'public/lib' as source directly
        },
        copy: {
            /**
             * DO NOT REMOVE. Task to copy npm modules to the public library folder.
             */
            npmLibs: {
                expand: true,
                cwd: 'node_modules',
                src: [
                    'react/umd/*.js',
                    'react-dom/umd/react-dom.development.js',
                    'react-dom/umd/react-dom.production.js'
                ], // Your library files
                dest: 'public/lib/'
            }
        }
    });
    
    // Load WP ReactJS Starter tasks (DO NOTE REMOVE this and initConfig should be called until here already)
    require('./build/grunt.js')(grunt);
    
    // Load npm tasks
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
    
    // Register default task
    grunt.registerTask('default', function() {
        grunt.log.write("Your default task...");
    });
};