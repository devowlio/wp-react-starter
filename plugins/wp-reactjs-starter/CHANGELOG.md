# Change Log

All notable changes to this project will be documented in this file.
See [Conventional Commits](https://conventionalcommits.org) for commit guidelines.

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
