#!/bin/sh

# Make the `lerna` command possible in GitLab CI environment

# Unshallow to make git-describe.sync work https://app.clickup.com/t/a6qmdj
git fetch --unshallow

# Use unsafe-perm so lerna lifecycle events work. Why: https://git.io/JeQdu
yarn config set unsafe-perm true

# lerna needs to detect the branch name without detached HEAD, Why: https://gitlab.com/gitlab-org/gitlab/issues/15409#note_214809980
[ ! "$CI_BUILD_TAG" ] && git checkout -B "$CI_COMMIT_REF_NAME" "$CI_COMMIT_SHA"

# Allow NPM publishing if enabled
[ $NPM_TOKEN ] && npm config set //registry.npmjs.org/:_authToken=$NPM_TOKEN -q

# Make `origin` in current repository work again, so push back to repo is possible. See https://gitlab.com/gitlab-org/gitlab/issues/14101#note_261684708
git remote set-url origin "https://gitlab-ci-token:$GITLAB_TOKEN@$CI_SERVER_HOST/$CI_PROJECT_PATH.git"