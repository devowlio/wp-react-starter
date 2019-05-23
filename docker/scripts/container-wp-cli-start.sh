#!/bin/bash

# Run original docker-entrypoint.sh because it is overwritten with "command" (https://git.io/)
curl ${DOCKER_ENTRYPOINT_GITHUB_URL} > docker-entrypoint.sh
chmod +x docker-entrypoint.sh
docker-entrypoint.sh apache2
rm docker-entrypoint.sh

# Install wp-cli
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod u=rwx,g=rx,o=rx wp-cli.phar
php wp-cli.phar --info --allow-root
mv wp-cli.phar /usr/local/bin/wp
. ~/.bashrc

## # Docker runs always in root, so alias wp (https://git.io/)
## #alias wp='wp --allow-root'
## # Download WordPress
## # wp --allow-root core download --version=${WORDPRESS_VERSION} # Version is defined in Dockerfile of image

# The database needs to be ready, so netcat it
apt-get update
apt-get -y install netcat
while ! nc -z mysql 3306;
do
    sleep 1;
done;

# Install wordpress itself
wp --allow-root core install --path="/var/www/html" --url="http://localhost" --title="wpdev" --admin_user=wordpress --admin_password=wordpress --admin_email=admin@test.com

# Config parameters
wp --allow-root config set WP_DEBUG true --raw
wp --allow-root config set WP_SCRIPT_DEBUG true --raw
wp --allow-root config set FS_METHOD direct # see https://git.io/fj4IK, https://git.io/fj4Ii

# # Activate this plugin
wp --allow-root plugin activate wp-reactjs-starter

# Delete unnecessery plugins
rm -rf wp-content/plugins/akismet
rm -rf wp-content/plugins/hello.php

### --------- THIS MUST BE AT THE END --------- ###
# Change the rights so media / plugins / themes are possible to upload and install
chown -R www-data:www-data /var/www/html/*

# Main CMD from https://git.io/fj4Fe
apache2-foreground






# Wait for mysql be ready
# while ! nc -z mysql 3306;
# do
#     sleep 1;
# done;
# 
# # Install wordpress itself
# wp core install --path="/var/www/html" --url="http://localhost" --title="wpdev" --admin_user=wordpress --admin_password=wordpress --admin_email=admin@test.com
# 
# # Config parameters
# wp config set WP_DEBUG true --raw
# wp config set WP_SCRIPT_DEBUG true --raw
# wp config set FS_METHOD direct # see https://git.io/fj4IK, https://git.io/fj4Ii
# 
# # Activate this plugin
# wp plugin activate wp-reactjs-starter
# 
# # Delete all inactivate plugins
# wp plugin delete $(wp plugin list --status=inactive --field=name)