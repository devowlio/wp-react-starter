FROM php:7.3-cli-stretch

# Avoid too many progress messages
ENV CI=1

# Apt and common dependencies
RUN apt-get update && apt-get install -y \
    # Commons
    sudo \
    autogen \
    wget \
    zip \
    unzip \
    tar \
    git \
    subversion \
    build-essential \
    apt-utils \
    software-properties-common \
    nasm \
    libjpeg-dev \
    libpng-dev \
    libpng16-16 \
    # Allow simple FTP uploads
    lftp \
    wput \
    # Pyhton is needed for Docker-Compose
    python \
    python-pip \
    python-dev \
    # Cypress.io dependencies
    xvfb \
    libgtk-3-dev \
    libnotify-dev \
    libgconf-2-4 \
    libnss3 \
    libxss1 \
    libasound2 && \
    # xdebug (can be activated with `docker-php-ext-enable xdebug`, not by default because it slows down PHP)
    pecl install xdebug && \
    # Composer
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer && \
    composer self-update --preview && \
    # Node
    curl -sL https://deb.nodesource.com/setup_12.x -o nodesource_setup.sh && \
    bash nodesource_setup.sh && \
    apt-get install -y nodejs && \
    # Yarn
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && apt-get install -y yarn && \
    # Docker and Docker-Compose (via Python, PiP)
    curl -sSL https://get.docker.com/ | sh && \
    pip install docker-compose

RUN npm install -g \
    # Global installations of npm packages
    wait-on && \
    composer global require \
    # Global installations of composer packages
    hirak/prestissimo
