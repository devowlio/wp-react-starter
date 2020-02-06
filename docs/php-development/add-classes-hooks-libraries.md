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
