module.exports = ({ file, options, env }) => ({
    plugins: {
        'autoprefixer': options.autoprefixer,
        'cssnano': env === 'production' ? options.cssnano : false
    }
});