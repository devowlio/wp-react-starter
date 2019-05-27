// @see https://vuepress.vuejs.org/config/#basic-config

// Allow also CI-based base-url /-/wp-reactjs-starter/-/jobs/219806015/artifacts/docs/php/
function basepath(env, dfltValue) {
    if (!env.CI) {
        return dfltValue;
    }

    if (env.VUEPRESS_PHP_BASE) {
        return env.VUEPRESS_PHP_BASE;
    }

    return "/-/" + env.CI_PROJECT_NAME + "/-/jobs/" + env.CI_JOB_ID + "/artifacts/docs/php/";
}

module.exports = {
    base: basepath(process.env, "/wp-content/plugins/wp-reactjs-starter/docs/php/"),
    themeConfig: {
        sidebar: ["/", "/classes", "/constants", "/functions", "/interfaces", "/traits"],
        displayAllHeaders: true
    },
    dest: "docs/php"
};
