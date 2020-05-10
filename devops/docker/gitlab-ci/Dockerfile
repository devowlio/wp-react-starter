FROM devowliode/wp-react-starter-gitlab-ci:php-7.3-cli-stretch

# Prepare our dependencies and cache
ARG GL_CI_WORKDIR
ENV CYPRESS_CACHE_FOLDER=/tmp$GL_CI_WORKDIR/.cypress
ENV YARN_CACHE_FOLDER=/tmp$GL_CI_WORKDIR/.yarn

# Set composer github token to avoid API rate limit (https://getcomposer.org/doc/articles/troubleshooting.md#api-rate-limit-and-oauth-tokens)
ARG PHP_COMPOSER_GITHUB_TOKEN
RUN (test $PHP_COMPOSER_GITHUB_TOKEN && \
    composer config -g github-oauth.github.com $PHP_COMPOSER_GITHUB_TOKEN) || :

# Install our dependencies into our gitlab runner
WORKDIR /tmp$GL_CI_WORKDIR

COPY install.tar .

RUN tar -xvf install.tar && \
    yarn bootstrap && \
    yarn cypress install

# Avoid too many progress messages
ENV CI=1