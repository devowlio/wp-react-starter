# Change Log

All notable changes to this project will be documented in this file.
See [Conventional Commits](https://conventionalcommits.org) for commit guidelines.

# 1.4.0 (2021-02-15)


### feat

* license change to GPL v3.0 and ISC


### fix

* do not use encodeURIComponent for url-parse parameters (fixes #30)





# 1.3.0 (2020-10-13)


### chore

* update dependencies (#3cj43t)


### docs

* update license agreement (#4ufx38)


### feat

* compatibility with WordPress 5.5 (#6gqcm8)
* introduce corrupt REST API notice (#4gkvbz)


### fix

* once a PHP notice is issued in the REST API, the interface still works (#6cpapc)





# 1.2.0 (2020-06-29)


### chore

* i18n update (#5ut991)


### feat

* allow to define licenses in root package.json (#68jvq7)
* use window.fetch with polyfill instead of jquery (#5whc87)


### fix

* correctly set the script translation text domain





## 1.1.2 (2020-05-20)


### build

* cleanup temporary i18n files correctly


### ci

* introduce node-gitlab-ci
* read root name from package.json


### fix

* add PATCH to available HTTP methods (#5cjaau)
* correctly enqueue dependencies (#52jf92)
* improvement speed up in admin dashboard (#52gj39)
* install database tables after reactivate plugin (#52k7f1)
* remove ~ due to G6 blacklist filtering (security plugins, #5cqdn0)





## 1.1.1 (2020-04-27)


### chore

* add hook_suffix to enqueue_scripts_and_styles function (#4ujzx0)


### fix

* use rest_url instead of site_url to generate REST url (#4pmk26)





# 1.1.0 (2020-04-15)


### build

* optional clean:webpackDevBundles grunt task to remove dev bundles in build artifact (#4jjq0u)


### ci

* correctly build i18n frontend files (#4jjq0u)
* run package jobs also on devops changes


### feat

* introduce php-scoper to scope our complete plugin dependencies (#4jnk84)
* provide global PHP functions in api folder (#4jnk84)


### fix

* enqueue composer scripts generates unique handle (#4cnu3q)
* typo in utils test file


### style

* reformat php codebase (#4gg05b)


### test

* show test reports in Gitlab MR (#4cg6tp)





## 1.0.3 (2020-03-25)


### chore

* update dependencies (#3cj43t)


### test

* configure jest setupFiles correctly with enzyme and clearMocks (#4akeab)





## 1.0.2 (2020-03-09)


### fix

* predefine tree shaking in own coding correctly (#3wkvfe) with vendor chunks (#3wnntb)
* usage of React while using Divi in dev environment (WP_DEBUG, #3rfqjk)





## 1.0.1 (2020-02-14)


### fix

* do not load script translations for libraries (#3mjh0e)
* enqueue ReactDOM version correctly (#3jm006)
* use own wp_set_script_translations to make it compatible with deferred scripts (#3mjh0e)
