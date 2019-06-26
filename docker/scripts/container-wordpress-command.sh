#!/bin/bash

# Run original docker-entrypoint.sh because it is overwritten with "command"
docker-entrypoint.sh apache2

# The database needs to be ready, so netcat it, and also the wp binary must be downloaded and executable
# Also we need the mysql-client so mysqldump can be used by WP-CLI
which netcat && echo "Skip apt-get installations because they are already installed on this volume"
which netcat || apt-get update
which netcat || apt-get -y install netcat mysql-client
while ! nc -z mysql 3306; do sleep 1; done;
while [ ! -f "/usr/local/bin/wp" -a ! -f "wp" ]; do sleep 1; done;
sleep 1; # Just to be sure the file is completely moved here

# Move the installed wp-cli to bin so we can execute it
test -f "wp" && mv /var/www/html/wp /usr/local/bin/
. ~/.bashrc

####################################################
### --------- DO NOT CHANGE UNTIL HERE --------- ###
####################################################

# Run the following scripts only when WordPress is started at first time
# Use always --allow-root because docker runs the service as root user
[[ $CI ]] && export WP_CI_INSTALL_URL="wordpress" || export WP_CI_INSTALL_URL="localhost"
if ! $(wp --allow-root core is-installed); then
    # Install wordpress itself
    wp --allow-root core install --path="/var/www/html" --url="http://$WP_CI_INSTALL_URL" --title="wpdev" --admin_user=wordpress --admin_password=wordpress --admin_email=admin@test.com

    # Config parameters
    wp --allow-root config set WP_DEBUG true --raw
    wp --allow-root config set SCRIPT_DEBUG true --raw
    wp --allow-root config set FS_METHOD direct # see https://git.io/fj4IK, https://git.io/fj4Ii

    # Permalink structure
    wp --allow-root rewrite structure '/%year%/%monthnum%/%postname%/' --hard

    # Activate this plugin
    wp --allow-root plugin activate wp-reactjs-starter

    # Delete unnecessery plugins
    rm -rf wp-content/plugins/akismet
    rm -rf wp-content/plugins/hello.php

    # Import startup.sql script if available
    test -f "/scripts/startup.sql" && wp --allow-root db import /scripts/startup.sql
fi

####################################################
### --------- THIS MUST BE AT THE END --------- ###
####################################################

# When CYPRESS test is active copy the e2e login plugin and activate it
cp /scripts/e2e-tests-autologin-plugin.php wp-content/plugins/
chown 33:33 wp-content/plugins/e2e-tests-autologin-plugin.php
wp --allow-root plugin activate e2e-tests-autologin-plugin

# Update only minor version
wp --allow-root core update --minor

# Change the rights so media / plugins / themes are possible to upload and install
chown -R www-data:www-data /var/www/html/*

# Main CMD from https://git.io/fj4Fe
apache2-foreground