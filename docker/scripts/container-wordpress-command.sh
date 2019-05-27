#!/bin/bash

# Run original docker-entrypoint.sh because it is overwritten with "command"
docker-entrypoint.sh apache2

# The database needs to be ready, so netcat it, and also the wp binary must be downloaded and executable
apt-get update && apt-get -y install netcat
while ! nc -z mysql 3306; do sleep 1; done;
while [ ! -f wp ]; do sleep 1; done;
sleep 1; # Just to be sure the file is completely moved here

# Move the installed wp-cli to bin so we can execute it
mv /var/www/html/wp /usr/local/bin/
. ~/.bashrc

####################################################
### --------- DO NOT CHANGE UNTIL HERE --------- ###
####################################################

# Install wordpress itself
wp --allow-root core install --path="/var/www/html" --url="http://localhost" --title="wpdev" --admin_user=wordpress --admin_password=wordpress --admin_email=admin@test.com

# Config parameters
wp --allow-root config set WP_DEBUG true --raw
wp --allow-root config set SCRIPT_DEBUG true --raw
wp --allow-root config set FS_METHOD direct # see https://git.io/fj4IK, https://git.io/fj4Ii

# Activate this plugin
wp --allow-root plugin activate wp-reactjs-starter

# Delete unnecessery plugins
rm -rf wp-content/plugins/akismet
rm -rf wp-content/plugins/hello.php

####################################################
### --------- THIS MUST BE AT THE END --------- ###
####################################################

# Update only minor version
wp --allow-root core update --minor

# Change the rights so media / plugins / themes are possible to upload and install
chown -R www-data:www-data /var/www/html/*

# Main CMD from https://git.io/fj4Fe
apache2-foreground