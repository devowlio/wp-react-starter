module.exports = function(grunt) {
    
    // Load WP ReactJS Starter tasks
    require('./build/grunt-makeyours.js')(grunt);
    
    // Project configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json')
    });
    
    // Register default task
    grunt.registerTask('default', function() {
        grunt.log.write("Your default task...");
    });
    
};