# Deploy wordpress.org

## Initial release

In this section it is explained how to release a new plugin to wordpress.org. Generally the initial release needs to be reviewed by the wordpress.org team so you have to prepare the installable plugin as `.zip` file locally. Later - when updating the plugin - the GitLab CI is used.

-   Add functionality to your plugin and prepare for release
-   Adjust [`wordpress.org/README.wporg.txt`](../usage/folder-structure/plugin.md#folder-structure) files (you can use [README validator](https://wordpress.org/plugins/developers/readme-validator/))
-   Prepare you images (header, icon, screenshots) in [`wordpress.org/assets`](../usage/folder-structure/plugin.md#folder-structure)
-   When you think it is ready to release
    -   Commit your work
    -   Let the CI/CD system [build the plugin for you](../advanced/build-production-plugin.md)
    -   Download the installable `.zip` from the Job artifacts
-   Navigate to [`build`](../usage/folder-structure/plugin.md#folder-structure) and you will se a generated `.zip` file
-   Upload file to https://wordpress.org/plugins/developers/add/ for review
-   Wait for approval

{% hint style="warning" %}
Sometimes it is important to directly send a an email to the review team because they should adjust the slug for you. By default the slug is generated through the `Plugin Name` in `index.php`. Imagine, your plugin is named `WP Real Media Library` your generated slug will be `wp-real-media-library`. But you want `real-media-library` to be the slug, send an email directly after upload process!
{% endhint %}

## Enable SVN deploy

When the above initial review got approved you can go on with deployment via CI/CD:

-   In your repository navigate to Settings > CI / CD > Variables
-   Add [variable](./extend-gitlab-ci-pipeline.md#available-variables) `WPORG_SVN_URL`: When the plugin gots approved you will get a SVN url, put it here
-   Add [variable](./extend-gitlab-ci-pipeline.md#available-variables) `WPORG_SVN_USERNAME`: The username of your wordpress.org user
-   Add [variable](./extend-gitlab-ci-pipeline.md#available-variables) `WPORG_SVN_PASSWORD`: The password of your wordpress.org user. You have to protect and - mask it. Note: If you password does not meet the requirements of [Masked Variables](https://gitlab.com/help/ci/variables/README#masked-variables) it does not work. It depends on you: Change your password so it works or leave it unmasked
-   Put some changes to `develop` branche and merge it to `master`
-   The CI/CD automatically deploys to wordpress.org
