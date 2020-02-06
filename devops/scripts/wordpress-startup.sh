#!/bin/bash

# Run your commands for startup of WordPress in the docker environment.
# If you want to do more customizations you should have a look at container-wordpress-command.sh

# Activate all the plugins
for slug in $PLUGIN_SLUGS
do
    wp --allow-root plugin activate $slug

    # Note: Before putting plugin-specific commands here, have a look at plugins/your-plugin/devops/scripts/wordpress-startup.sh!
done

# Delete unnecessery plugins
rm -rf wp-content/plugins/akismet
rm -rf wp-content/plugins/hello.php