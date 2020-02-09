/**
 * Unfortunely VuePress does not yet support typescript language for
 * configuration files. So we go a simple workaround an `tsc` that file while
 * moving into `.vuepress` folder.
 *
 * @see https://v1.vuepress.vuejs.org/guide/basic-config.html#config-file
 * @see https://vuepress.vuejs.org/config/#basic-config
 */

/**
 * In CI set the basepath to "/vuepress-basepath-replacement/" so you can use `sed`
 * for find and replace. Why? VuePress does not support relative pathes.
 *
 * Use this in your jobs if you deploy to a new basepath url:
 *
 * ```
 * find ./docs/php -type f -exec sed -i -e 's/vuepress-basepath-replacement/my-new\/path/g' {} \;
 * ```
 *
 * @param dfltValue
 * @see https://github.com/vuejs/vuepress/issues/796
 */
function basepath(dfltValue: string) {
    if (!process.env.CI) {
        return dfltValue;
    }

    if (process.env.VUEPRESS_PHP_BASE) {
        return process.env.VUEPRESS_PHP_BASE;
    }

    return "/vuepress-basepath-replacement/";
}

const slug = process.env.npm_package_slug;
module.exports = {
    base: basepath(`/wp-content/plugins/${slug}/docs/php/`),
    themeConfig: {
        sidebar: ["/", "/classes", "/constants", "/functions", "/interfaces", "/traits"],
        displayAllHeaders: true
    },
    dest: "docs/php"
};
