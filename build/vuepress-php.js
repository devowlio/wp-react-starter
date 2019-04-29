// @see https://vuepress.vuejs.org/config/#basic-config
module.exports = {
    base: '/wp-content/plugins/wp-reactjs-starter/docs/php/',
    themeConfig: {
        sidebar: [
            '/',
            '/classes',
            '/constants',
            '/functions',
            '/interfaces',
            '/traits'
        ],
        displayAllHeaders: true
    },
    dest: 'docs/php'
};