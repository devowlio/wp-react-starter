# Add new classes, hooks and libraries

## Add new class

The boilerplate comes with a class [`Core`](predefined-classes.md#core). It does a lot of things automatically, including [**namespacing** ](https://www.php.net/manual/language.namespaces.php)and **require composer dependencies**.

If you want to create your own classes / interfaces / enums, ... please create them in [`src/inc/`](../usage/folder-structure/plugin.md#folder-structure) with `MyClass.php`. The `inc` **folder hierarchy represents the namespace prefix**. For example, create a class in `inc/my/package/MyClass.php`, it can be consumed in that way:

```php
namespace MatthiasWeb\WPRJSS;
use MatthiasWeb\WPRJSS\base;
use MatthiasWeb\WPRJSS\my\package;

// [...]
new package\MyClass();
// [...]

# It also works for interfaces and enums
class AnotherClass implements package\IMyInterface
{
    use base\UtilsProvider;
}
```

{% hint style="info" %}
Replace `MatthiasWeb\WPRJSS` with your defined namespace. Also, do not forget to always use `base\UtilsProvider` trait!
{% endhint %}

## Add new hooks

[Actions and Filters](https://developer.wordpress.org/plugins/hooks/) are the heart of WordPress. It allows **modifying behavior through callbacks**. Learn, what they do and if you want to register a new hook, simply put it to [`Core#init`](predefined-classes.md#core). It is a good practice to put them all in one file to keep the clean overview.

{% hint style="info" %}
Of course there are exceptions not putting all actions & filters to the `init` method, but you won't have them in the beginning.
{% endhint %}

## Add new library

Like [npmjs.com](https://www.npmjs.com/) there is something similar for PHP, too. `composer` is the package manager coming automatically with the boilerplate. You can install any dependency from [packagist.org](https://packagist.org/).

When the composer dependency supports **autoloading** out-of-the-box, do not worry about `include`. The boilerplate already includes the vendor autoload and you simply use by the exposed `namespace` (its mostly documented in the given dependency).

{% hint style="info" %}
Before adding new dependencies, you should determine if its a development dependency or needed dependency for the plugin. Non-dev dependencies are also bundled with the installable plugin. The `build` build does not contain any dev dependencies.
{% endhint %}

{% page-ref page="../advanced/create-package.md" %}

## Stubs

### Stubs, what?

> STUBS are normal, syntactically correct PHP files that contain function & class signatures, constant definitions, etc. for all built-in PHP stuff and most standard extensions. Stubs need to include complete PHPDOC, especially proper @return annotations - [quote phpstorm-stubs](https://github.com/JetBrains/phpstorm-stubs#phpstorm-stubs).

But stubs can not be only used for PHP built-in functions and classes, they can also be generated for WordPress or created yourself. Scenario WordPress: A generator simply consumes the complete WordPress core coding, extracts all functions and classes and removes the implementation itself. The result is a file containing all definitions with documentation.

If you are more familiar with the JavaScript ecosystem: `stubs = TypeScript typings`.

### Why do I need stubs?

> An IDE needs them for completion, code inspection, type inference, doc popups, etc. Quality of most of these services depend on the quality of the stubs (basically their PHPDOC @annotations) - [quote phpstorm-stubs](https://github.com/JetBrains/phpstorm-stubs#phpstorm-stubs).

Imagine you code inside a project with VSCode and you use an external dependency, but you do not have the coding of the dependency itself - you will never obtain intellisense / autocompletion. In our case it's WordPress: We are coding our plugins and packages within a monorepo but the WordPress core coding is never in our repository. Gladly, WordPress provides a stub for their complete codebase: [wordpress-stubs](https://github.com/php-stubs/wordpress-stubs)

**wordpress-stubs is a predefined dependency of WP React Starter, you do not need to add it manually!**

### Add stubs

You have two options to add stubs to your plugin. No matter which way you take, the most important thing is that you add your stub to `package.json#stubs` so it is also considered in build process:

```json
{
    "stubs": ["./vendor/php-stubs/wordpress-stubs/wordpress-stubs.php", "./scripts/stubs.php"]
}
```

{% hint style="info" %}
The build process uses [PHP-Scoper](https://github.com/humbug/php-scoper), it prefixes all PHP namespaces in a file/directory to isolate the code bundled. We can only point out how important it is to properly integrate stubs.
{% endhint %}

#### Define stubs yourself

Add a file `plugins/your-plugin/scripts/stubs.php` and put your stubs inside it. It can e. g. look like this:

```php
<?php

/**
 * Set the sort order of a term.
 *
 * @param int    $term_id   Term ID.
 * @param int    $index     Index.
 * @param string $taxonomy  Taxonomy.
 * @param bool   $recursive Recursive (default: false).
 * @return int
 */
function wc_set_term_order($term_id, $index, $taxonomy, $recursive = false)
{
    // Silence is golden.
}
```

{% hint style="info" %}
In `common/stubs.php` you will find a common stubs file which you can modify for all your plugins. The above example shows per-plugin stubbing.
{% endhint %}

#### As dependency

The best use case for this is if you are developing a plugin for WooCommerce and want to use WooCommerce functionality inside your plugin coding. WooCommerce is great and also offers a stub via [composer](https://packagist.org/packages/php-stubs/woocommerce-stubs). Navigate to your plugins folder and execute:

```bash
composer require --dev php-stubs/woocommerce-stubs
```

Do not forget to add the stub to your `package.json#stubs` as mentioned above:

```json
{
    "stubs": [
        "./vendor/php-stubs/wordpress-stubs/wordpress-stubs.php",
        "./vendor/php-stubs/woocommerce-stubs/woocommerce-stubs.php"
    ]
}
```
