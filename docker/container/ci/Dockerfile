FROM ubuntu:18.04

ENV TZ=UTC

RUN export LC_ALL=C.UTF-8
RUN DEBIAN_FRONTEND=noninteractive
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Common tools
RUN apt-get update
RUN apt-get install -y \
    sudo \
    autoconf \
    autogen \
    language-pack-en-base \
    wget \
    zip \
    unzip \
    curl \
    git \
    subversion \
    build-essential \
    apt-utils \
    software-properties-common \
    nasm \
    libjpeg-dev \
    libpng-dev \
    libpng16-16 \
    # Pyhton is needed for Docker-Compose
    python \
    python-pip \
    python-dev

# PHP
RUN LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php && apt-get update && apt-get install -y php7.2
RUN apt-get install -y \
    php7.2-curl \
    php7.2-gd \
    php7.2-dev \
    php7.2-xml \
    php7.2-bcmath \
    php7.2-mysql \
    php7.2-mbstring \
    php7.2-zip \
    php7.2-bz2 \
    php7.2-soap \
    php7.2-json \
    php7.2-intl \
    php7.2-imap \
    php7.2-imagick

# Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer && \
    composer self-update --preview

# Node.js
RUN curl -sL https://deb.nodesource.com/setup_10.x -o nodesource_setup.sh
RUN bash nodesource_setup.sh
RUN apt-get install -y nodejs

# Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get install -y yarn

# Docker and Docker-Compose (via Python, PiP)
RUN curl -sSL https://get.docker.com/ | sh
RUN pip install docker-compose

# Display versions installed
RUN php -v
RUN composer --version
RUN node -v
RUN npm -v
RUN yarn -v
RUN docker -v
RUN docker-compose -v

# Global installations via npm
RUN npm install -g wait-on

# Cypress.io
RUN apt-get install -y \
    xvfb \
    libgtk-3-dev \
    libnotify-dev \
    libgconf-2-4 \
    libnss3 \
    libxss1 \
    libasound2

# wput for FTP uploads
RUN apt-get install -y wput