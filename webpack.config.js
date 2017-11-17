var path = require( 'path' ),
	webpack = require( 'webpack' ),
	exec = require('child_process').exec,
	NODE_ENV = process.env.NODE_ENV || 'development',
	ExtractTextPlugin = require( 'extract-text-webpack-plugin' ),
	bourbonIncludePath = require('node-bourbon').includePaths,
	dist = path.join( __dirname, 'public', NODE_ENV === 'production' ? 'dist' : 'dev' );

module.exports = {
	entry: {
		widget: './public/src/widget.js',
		admin: './public/src/admin.js'
	},
	output: {
		path: dist,
		filename: "[name].js"
	},
	externals: {
		'react': 'React',
		'react-dom': 'ReactDOM'
	},
	devtool: '#source-map',
	module: {
	    rules: [{
	    	test: /\.js$/,
			exclude: /(disposables)/,
			use: 'babel-loader?cacheDirectory'
	    }, {
	    	test: /\.scss$/,
	        use: ExtractTextPlugin.extract({
	    		fallback: 'style-loader',
	        	use: ['css-loader', {
					loader: 'postcss-loader',
					options: {
						config: {
							ctx: {
								cssnano: true,
								autoprefixer: true
							}
						}
					}
				}, 'sass-loader?includePaths[]=' + bourbonIncludePath]
	        })
	    }]
	},
	resolve: {
		extensions: [ '.js', '.jsx' ],
		modules: [ 'node_modules', 'public/src' ]
	},
	plugins: [
		new webpack.DefinePlugin({
			// NODE_ENV is used inside React to enable/disable features that should only be used in development
			'process.env': {
				NODE_ENV: JSON.stringify( NODE_ENV ),
				env: JSON.stringify( NODE_ENV )
			}
		}),
		new ExtractTextPlugin( '[name].css' ),
		new ((function() {
			// Short plugin to run script on build finished to recreate the cachebuster
			function WebPackRecreateCachebuster() { }
			WebPackRecreateCachebuster.prototype.apply = function(compiler) {
				compiler.plugin('done', function(compilation, callback) {
					setTimeout(function() { console.log('Running webpack-build-done script...'); }, 0);
					exec('npm run webpack-build-done', function(error, stdout, stderr) { console.log(stdout); });
				});
			};
			return WebPackRecreateCachebuster;
		})())()
	].concat(NODE_ENV === 'production' ? [
		new webpack.optimize.UglifyJsPlugin({
			compress: {
				warnings: false
			}
		})
	] : [])
};