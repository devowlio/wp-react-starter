/**
 * Unfortunely VuePress does not yet support typescript language for
 * configuration files. So we go a simple workaround an `tsc` that file while
 * moving into `.vuepress` folder.
 *
 * @see https://v1.vuepress.vuejs.org/guide/basic-config.html#config-file
 * @see https://vuepress.vuejs.org/config/#basic-config
 */

// Allow also CI-based based-url /-/my-plugin/-/jobs/219806015/artifacts/docs/php/
function basepath(dfltValue: string) {
    if (!process.env.CI) {
        return dfltValue;
    }

    if (process.env.VUEPRESS_PHP_BASE) {
        return process.env.VUEPRESS_PHP_BASE;
    }

    return `/-/${process.env.CI_PROJECT_NAME}/-/jobs/${process.env.CI_JOB_ID}/artifacts/docs/php/`;
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
