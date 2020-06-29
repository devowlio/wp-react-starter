# License checker

Licenses can be a mess. So, we decided to do this job for you, too. We introduced two license scanners not allowing you to use dependencies with unwanted licenses. All packages and plugins are licensed out-of-the-box `GPL-3.0-or-later` (see `LICENSE` files and `license` key in `package.json``composer.json`).

{% hint style="warning" %}
We give no guarantee of legal validity!
{% endhint %}

## JavaScript

As we use `yarn` as dependency manager, we can rely on [`yarn licenses`](https://yarnpkg.com/lang/en/docs/cli/licenses/) scanning all used dependencies and report back if there is an issue (on [CI side](../gitlab-integration/predefined-pipeline.md#validate)). A generated disclaimer will be saved to [`LICENSE_3RD_PARTY_JS.md`](../usage/folder-structure/plugin.md#folder-structure).

Allowed licenses and packages can be configured in [`package.json#license-check`](../usage/folder-structure/plugin.md#folder-structure) or the root `package.json`.

{% hint style="warning" %}
Root dependencies are not checked! Make sure to add all your license-relevant dependencies to your subpackage [`package.json`](../usage/folder-structure/plugin.md#folder-structure).
{% endhint %}

## PHP

As we use `composer` as dependency manager, we can rely on the following packages:

-   [`composer-plugin-license-check`](https://packagist.org/packages/metasyntactical/composer-plugin-license-check): Checks licenses due to a whitelist defined in [`composer.json#extra.metasyntactical/composer-plugin-license-check`](../usage/folder-structure/folder.md#folder-structure) and and reports if there is an issue (on [CI side](../gitlab-integration/predefined-pipeline.md#validate))
-   [`php-legal-licenses`](https://packagist.org/packages/comcast/php-legal-licenses): Additionally a [`LICENSE_3RD_PARTY_PHP.md`](../usage/folder-structure/plugin.md#folder-structure) file will be generated.
