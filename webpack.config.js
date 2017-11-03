require( 'es6-promise' ).polyfill();

var path = require( 'path' );
var webpack = require( 'webpack' );
var NODE_ENV = process.env.NODE_ENV || 'development';
var ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
var dist = path.join( __dirname, 'public', 'dist' );

var webpackConfig = {
	entry: {
		widget: './public/src/widget.js',
		admin: './public/src/admin.js'
	},
	output: {
		path: dist,
		filename: "[name].js"
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
	          use: ['css-loader', 'sass-loader']
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
				NODE_ENV: JSON.stringify( NODE_ENV )
			}
		}),
		new ExtractTextPlugin( '[name].css' )
	]
};

if ( NODE_ENV === 'production' ) {
	webpackConfig.plugins.push( new webpack.optimize.UglifyJsPlugin({
		compress: {
			warnings: false
		}
	}) );
}

module.exports = webpackConfig;