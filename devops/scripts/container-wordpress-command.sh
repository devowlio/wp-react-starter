#!/bin/bash

# Run common commands for startup of WordPress in the docker environment.

# Run original docker-entrypoint.sh because it is overwritten with "command"
docker-entrypoint.sh apache2
__dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# The database needs to be ready, so netcat it, and also the wp binary must be downloaded and executable
# Also we need the mariadb-client so mysqldump can be used by WP-CLI
which netcat && echo "Skip apt-get installations because they are already installed on this volume"
which netcat || apt-get update
which netcat || apt-get -y install netcat mariadb-client
while ! nc -z mysql 3306; do sleep 1; done;
while [ ! -f "/usr/local/bin/wp" -a ! -f "wp" ]; do sleep 1; done;
sleep 1; # Just to be sure the file is completely moved here

# Move the installed wp-cli to bin so we can execute it
test -f "wp" && mv /var/www/html/wp /usr/local/bin/
. ~/.bashrc

# Wait for all the installable plugins because they can be lazy (e. g. through e2e or traefik)
for slug in $PLUGIN_SLUGS
do
    echo "Wait for folder $slug in $(pwd)/wp-content/plugins ..."
    while [ ! -d "wp-content/plugins/$slug" ]; do sleep 1; done;
done

# Run the following scripts only when WordPress is started at first time
# Use always --allow-root because docker runs the service as root user
[ $WP_CI_INSTALL_URL ] && export _WP_CI_INSTALL_URL="$WP_CI_INSTALL_URL" || export _WP_CI_INSTALL_URL="http://localhost:8080"
if ! $(wp --allow-root core is-installed); then
    # Install wordpress itself
    wp --allow-root core install --path="/var/www/html" --url="$_WP_CI_INSTALL_URL" --title="wpdev" --admin_user=wordpress --admin_password=wordpress --admin_email=admin@test.com

    # Config parameters
    wp --allow-root config set WP_DEBUG true --raw
    wp --allow-root config set SCRIPT_DEBUG true --raw
    wp --allow-root config set FS_METHOD direct # see https://git.io/fj4IK, https://git.io/fj4Ii

    # Permalink structure
    wp --allow-root rewrite structure '/%year%/%monthnum%/%postname%/' --hard

    # Import startup.sql script if available
    test -f "/scripts/startup.sql" && wp --allow-root db import /scripts/startup.sql
fi

# Call startup scripts (main and plugin specific)
source /scripts/wordpress-startup.sh
for f in $(ls /scripts/plugins/*/wordpress-startup.sh 2>/dev/null); do
   source $f
done

# When CYPRESS test is active copy the e2e login plugin and activate it
cp /scripts/e2e-tests-autologin-plugin.php wp-content/plugins/
chown 33:33 wp-content/plugins/e2e-tests-autologin-plugin.php
wp --allow-root plugin activate e2e-tests-autologin-plugin

# Update only minor version
wp --allow-root core update --minor

# Change the rights so media / plugins / themes are possible to upload and install
echo Chown www-data...
chown -R www-data:www-data /var/www/html/wp-content/uploads
chown www-data:www-data /var/www/html/wp-content /var/www/html/wp-content/plugins /var/www/html/wp-content/themes /var/www/html/wp-content/upgrade
echo Chown www-data done!

# Main CMD from https://git.io/fj4Fe
apache2-foreground