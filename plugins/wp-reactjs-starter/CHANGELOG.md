# Change Log

All notable changes to this project will be documented in this file.
See [Conventional Commits](https://conventionalcommits.org) for commit guidelines.

# 1.4.0 (2021-02-15)


### feat

* license change to GPL v3.0 and ISC





# 1.3.0 (2020-10-13)


### chore

* update dependencies (#3cj43t)


### ci

* try to use cache when building plugin and untouched


### docs

* update license agreement (#4ufx38)


### feat

* compatibility with WordPress 5.5 (#6gqcm8)


### test

* disable cypress test which does no longer with cypress@4





# 1.2.0 (2020-06-29)


### build

* improve plugin build with webpack parallel builds


### chore

* i18n update (#5ut991)


### feat

* allow to define licenses in root package.json (#68jvq7)
* use window.fetch with polyfill instead of jquery (#5whc87)
* use window.fetch with polyfill instead of jquery (#5whc87)





## 1.1.2 (2020-05-20)


### build

* cleanup temporary i18n files correctly


### ci

* introduce node-gitlab-ci
* read root name from package.json


### fix

* correctly enqueue dependencies (#52jf92)





## 1.1.1 (2020-04-27)


### chore

* add hook_suffix to enqueue_scripts_and_styles function (#4ujzx0)


### test

* automatically retry cypress tests (#3rmp6q)





# 1.1.0 (2020-04-15)


### chore

* update to Cypress v4 (#2wee38)


### ci

* correctly build i18n frontend files (#4jjq0u)
* run package jobs also on devops changes


### feat

* introduce php-scoper to scope our complete plugin dependencies (#4jnk84)


### style

* reformat php codebase (#4gg05b)


### test

* avoid session expired error in E2E tests (#3rmp6q)
* show test reports in Gitlab MR (#4cg6tp)





## 1.0.3 (2020-03-25)


### chore

* update dependencies (#3cj43t)


### fix

* exclude public i18n strings in backend .pot file


### style

* run prettier@2 on all files (#3cj43t)


### test

* configure jest setupFiles correctly with enzyme and clearMocks (#4akeab)





## 1.0.2 (2020-03-09)


### fix

* eliminate circular dependencies in factory functions (#3rme4g)
* predefine tree shaking in own coding correctly (#3wkvfe) with vendor chunks (#3wnntb)
* remove vuepress-php because it's not really usable anymore (#3uh3yt)





## 1.0.1 (2020-02-14)


### ci

* improve composer install and publishing of docs


### fix

* old PHP versions (7.1) reported a bug related to namespaces
* use own wp_set_script_translations to make it compatible with deferred scripts (#3mjh0e)
