# Create package

As mentioned [here](../php-development/predefined-classes.md) and [here](../typescript-development/utils-package.md), we are using a **multi-package-/mono repository** that means we can use the advantages of modularized code. _What does this exactly mean?_ Code, which you use more than once, put it into an own package. This works for TypeScript code and also for PHP code.

Beside the [`packages/utils`](../usage/folder-structure/root.md#folder-structure) package you can create another package. It is recommend to not modify the utils package so you can easier migrate to new versions of WP ReactJS Starter. So, let's start and create a new package:

```
create-wp-react-app create-package
```

You will get some prompts and afterwards a new package will be available in [`packages/`](../usage/folder-structure/root.md#folder-structure).

## Consume package PHP in plugin

The previously created package contains a `src` folder. There you can put all your shared PHP classes. Each created plugin can consume local packages from [`packages/`](../usage/folder-structure/root.md#folder-structure) as the [`path`](https://getcomposer.org/doc/05-repositories.md#path) is configured already properly.

```sh
cd plugins/my-plugin # Navigate to our plugin
composer require "wp-reactjs-multi-starter/mypackage @dev" # @dev is important so symlinking works as expected!
```

### Example

So, let's imagine we have the file `packages/mypackage/src/Example.php`. In your plugins' coding you can access the class simply with the defined PHP namespace:

```php
new \MatthiasWeb\MyPackage\Example();
```

{% hint style="info" %}
You have to replace `wp-reactjs-multi-starter` / `MatthiasWeb\MyPackage` with your names.
{% endhint %}

{% hint style="warning" %}
Our predefined GitLab integration does not currently support automatic publish to packagist.org as the local composer dependencies are bundled together with the plugin.
{% endhint %}

### Dynamically get plugin data

_What?_ You heard correctly, you can dynamically get plugin data in your composer package. Due to the fact you inject [`UtilsProvider`](../php-development/add-classes-hooks-libraries.md#add-new-class) in all your classes you can get dynamically plugin data. That means, if you call a method of a modularized composer package in your plugin, you simply can obtain a constant like this in your composer package:

```php
// [...]

trait MyAwesomeTrait
{
    public function foo()
    {
        // Dynamically gets the WPRJSS_PATH variable. See available constant to get more.
        $pluginPath = $this->getPluginConstant("PATH");

        // Dynamically create a new instance of the plugins class "Activator"
        $pluginInstance = $this->getPluginClassInstance("Activator");

        // Dynamically get the core instance of the plugin
        $pluginCore = $this->getCore();
    }
}
```

{% hint style="warning" %}
This only works with traits!
{% endhint %}

## Consume package JavaScript in plugin

The previously created package also contains a `lib` folder. There you can put all your shared TypeScript files. Due to the fact we are using `lerna` using packages as simple as:

```sh
yarn lerna add @wp-reactjs-multi-starter/mypackage --scope @wp-reactjs-multi-starter/myplugin
yarn lerna link
```

Afterwards you need to enqueue that package to your plugin. _Why?_ All packages are so-called externals and WordPress handles it with an own system: Each package / module has an own handle and exposes functionality. Navigate to your [`Assets.php`](../php-development/predefined-classes.md#assets) and enqueue the package:

```php
$this->enqueueComposerScript('mypackage');
```

In your plugins' coding you can access the files simply as follow:

```ts
import /* [...] */ "@wp-reactjs-multi-starter/mypackage";
```

{% hint style="info" %}
You have to replace `wp-reactjs-multi-starter` with your names.
{% endhint %}

## Localization

A package can also be localized with commands [`yarn i18n:backend` and `yarn i18n:generate:frontend`](../usage/available-commands/package.md#localization). All `.pot` files will be generated to `languages`. If you consume a package via `enqueueComposerScript` and `composer require` all localization files are **automatically** loaded to the WordPress runtime, you do not have to manually load them (Provider)! But, you still need to register the hooks manually as explained below (Consumer).

### PHP Consumer

First, you need to create a new file `/packages/your-package/src/Localization.php` with the following content:

```php
<?php
namespace WPRJSS\MyPackage;

use WPRJSS\Utils\PackageLocalization;

// @codeCoverageIgnoreStart
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request
// @codeCoverageIgnoreEnd

/**
 * Package localization for `your-package` package.
 */
class Localization extends PackageLocalization
{
    /**
     * C'tor.
     */
    protected function __construct()
    {
        parent::__construct(MY_PACKAGE_ROOT_SLUG, dirname(__DIR__));
    }

    /**
     * Put your language overrides here!
     *
     * @param string $locale
     * @return string
     * @codeCoverageIgnore
     */
    protected function override($locale)
    {
        // switch ($locale) {
        //     // Put your overrides here!
        //     case 'de_AT':
        //     case 'de_CH':
        //     case 'de_CH_informal':
        //     case 'de_DE_formal':
        //         return 'de_DE';
        //         break;
        //     default:
        //         break;
        // }
        return $locale;
    }
}
```

In your main PHP file (e. g. `Core.php`) you need to make sure to define the following constants and create the `Localization` instance. Afterwards, you can use `__` with `MY_PACKAGE_TD` as your text domain!

```php
define('MY_PACKAGE_ROOT_SLUG', 'wp-reactjs-multi-starter');
define('MY_PACKAGE_SLUG', 'my-package');
define('MY_PACKAGE_TD', MY_PACKAGE_ROOT_SLUG . '-' . MY_PACKAGE_SLUG);
(new Localization())->hooks();
```

### TypeScript Consumer

In your TypeScript coding it is much more easier to consume the translations:

```tsx
import { createLocalizationFactory } from "@wp-react-multi-starter/utils";

const { __, _i /* [...] */ } = createLocalizationFactory(`${process.env.rootSlug}-${process.env.slug}`);
```
