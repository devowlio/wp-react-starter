module.exports = ({ file, options, env }) => ({
    plugins: [
        require('autoprefixer')
    ].concat(env === 'production' ? [require('postcss-clean')(options.clean)] : [])
});