var prompt = require('prompt'),
    _ = require("lodash");

module.exports = function(grunt) {
    
    grunt.registerTask('wordpress-reactjs-starter-makeyours', function() {
        // Check already generated
        if (grunt.file.exists('./build/.generated')) {
            throw new Error('You already have generated the boilerplate.');
        }
        
        // Start
        var done = this.async(),
            tmpl = grunt.file.read("./build/grunt-makeyours-index.tmpl");
    
        prompt.start();
        prompt.get({
            properties: {
                pluginName: {
                    message: 'Step 1 / 12: Plugin name',
                    default: 'WP ReactJS Starter'
                },
                pluginURI: {
                    message: 'Step 2 / 12: Plugin URI',
                    default: 'https://github.com/matzeeeeeable/wp-reactjs-starter'
                },
                pluginDescription: {
                    message: 'Step 3 / 12: Plugin Description',
                    default: 'This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin.'
                },
                author: {
                    message: 'Step 4 / 12: Plugin author',
                    required: true
                },
                authorURI: {
                    message: 'Step 5 / 12: Plugin author URI'
                },
                version: {
                    message: 'Step 6 / 12: Plugin initial version',
                    default: '0.1.0'
                },
                textDomain: {
                    description: 'Step 7 / 12: Plugin slug for text domain and language files (example: wp-reactjs-starter)',
                    pattern: /^[^ ]+$/,
                    message: 'The plugin slug may not contain whitespaces',
                    required: true
                },
                minPHP: {
                    message: 'Step 8 / 12: Minimum PHP version',
                    default: '5.3.0'
                },
                namespace: {
                    description: 'Step 9 / 12: PHP file namespace prefix (example: MatthiasWeb\\WPRJSS)',
                    pattern: /^[^ ]+$/,
                    message: 'The namespace may not contain whitespaces',
                    required: true,
                    before: function(value) {
                        return _.trim(value, '\\').split('\\').join('\\\\');
                    }
                },
                optPrefix: {
                    description: 'Step 10 / 12: WordPress option names prefix (example: wprjss)',
                    pattern: /^[A-Za-z0-9]+$/,
                    message: 'The option prefix must match the [A-Za-z0-9] pattern',
                    before: function(value) {
                        return value.toLowerCase();
                    },
                    required: true
                },
                dbPrefix: {
                    description: 'Step 11 / 12: WordPress database tables prefix (example: wprjss)',
                    pattern: /^[A-Za-z0-9]+$/,
                    message: 'The database table prefix must match the [A-Za-z0-9] pattern',
                    before: function(value) {
                        return value.toLowerCase();
                    },
                    required: true
                },
                constantPrefix: {
                    description: 'Step 12 / 12: PHP constants prefix for the above options (example: WPRJSS)',
                    pattern: /^[A-Za-z0-9]+$/,
                    message: 'The constant prefix must match the [A-Za-z0-9] pattern',
                    before: function(value) {
                        return value.toUpperCase();
                    },
                    required: true
                }
            }
        }, function (e, result) {
            // We have all the informations, let's parse the index.php file
            var indexPHP = tmpl;
            _.each(result, function(value, key) {
                indexPHP = indexPHP.replace(new RegExp('\\$\\{' + key + '\\}', 'g'), value);
            });
            
            // Create index.php file
            grunt.log.writeln('Creating index.php file...');
            grunt.file.write('./index.php', indexPHP);
            grunt.file.write('./build/.generated', JSON.stringify(result));

            // Read all available constants
            grunt.log.writeln('Fetching all available constant names...');
            var m, regex = /define\(\'([^\']+)/g, constants = [];
            while ((m = regex.exec(indexPHP)) !== null) {
                if (m.index === regex.lastIndex) {
                    regex.lastIndex++;
                }
                
                m.forEach((match, groupIndex) => {
        			if (groupIndex === 1) {
        				constants.push(match);
        			}
                });
            }
            grunt.log.writeln('Found the following constants: ' + constants.join(', '));
            
            // Apply constants and namespaces
            var fileContent, file, files = grunt.file.expand({
                cwd: './inc'
            }, "**/*"), parseOldConstant = function(constant) {
                return 'WPRJSS' + constant.slice(result.constantPrefix.length);
            }, functions = ['wprjss_skip_php_admin_notice'];
            _.each(files, function(_file) {
                file = './inc/' + _file;
                if (grunt.file.isFile(file)) {
                    grunt.log.writeln('Create constants, namespaces and procedural functions in [' + file + '] ...');
                    fileContent = grunt.file.read(file);
                    
                    // Replacing the constants in /inc files
                    _.each(constants, function(constant) {
                        fileContent = fileContent.replace(new RegExp(parseOldConstant(constant), 'g'), constant);
                    });
                    
                    // Replacing the namespaces in /inc files
                    fileContent = fileContent.replace(new RegExp('MatthiasWeb\\\\WPRJSS', 'g'), result.namespace);
                    
                    // Apply for procedural functions
                    _.each(functions, function(fnName) {
                        fileContent = fileContent.replace(new RegExp(fnName, 'g'), fnName.replace('wprjss', result.optPrefix));
                    });
                    
                    grunt.file.write(file, fileContent);
                }
            });
            
            // Apply for language files
            grunt.log.writeln('Create language files...');
            var potFile = './languages/wp-reactjs-starter.pot', potContent = grunt.file.read(potFile);
            grunt.file.delete(potFile);
            grunt.file.write('./languages/' + result.textDomain + '.pot', potContent.replace('WP ReactJS Starter', result.pluginName));
            
            grunt.log.ok('All files successfully created. Please read on the Documentation on https://github.com/matzeeeeeable/wp-reactjs-starter for more informations. Happy coding and moke something awesome. :-)');
            done();
        });
    });
    
};