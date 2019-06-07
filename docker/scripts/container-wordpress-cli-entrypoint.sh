#!/bin/bash

# Copy the installed WP-CLI binary so it can be used through the wordpress environment
# You do not have to edit this file!

cp /usr/local/bin/wp /var/www/html/

# Check if WP is already installed and running and start the webpack watcher
if $(wp --allow-root core is-installed); then
    # Create lock file so the host knows the WordPress installation is up and running
    [[ $CI ]] && export WP_CI_INSTALL_URL="wordpress" || export WP_CI_INSTALL_URL="localhost"
    echo $WP_CI_INSTALL_URL > /scripts/.env-wp.lock
fi