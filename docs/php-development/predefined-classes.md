# Predefined classes

When navigating to [`plugins/your-plugin/src/inc`](../usage/folder-structure/plugin.md#folder-structure) you will find the below predefined classes you should use.

As we are using a **multi-package-/mono repository** we have the ability and advantage of **modular** package **development**. That means, coding which should be used in multiple plugins can be **outsourced** to an own package. This can also be done for TypeScript ([`packages/utils/lib`](../usage/folder-structure/root.md#folder-structure)), but this does not matter here.

{% page-ref page="../advanced/create-package.md" %}

With [`create-wp-react-app create-workspace`](../usage/getting-started.md#create-workspace) a main utils package in [`packages/utils`](../usage/folder-structure/root.md#folder-structure) is generated automatically.

## Activator

`Activator.php`: The activator class handles the plugin relevant activation hooks: **Activation, deactivation** and **installation**. In this context, "installation" means installing needed database tables.

`activate()`: Method gets fired when the user **activates** the plugin.

`deactivate()`: Method gets fired when the user **deactivates** the plugin.

`dbDelta()`: Install tables, stored procedures or whatever in the database. This method is always called when the **version bumps** or for the **initial activation**.

{% hint style="info" %}
If you want to implement an **uninstall** execution, have a look at `src/uninstall.php`. Do not clean user data / database tables in the uninstall process - it is good practice to provide a custom button to the user (perhaps in Settings?).
{% endhint %}

## Assets

`Assets.php`: Asset management for frontend **scripts and styles**.

`enqueue_scripts_and_styles($type)`: Enqueue scripts and styles depending on the type. This function is called from both [`admin_enqueue_scripts`](https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/) and [`wp_enqueue_scripts`](https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/). You can check the type through the `$type` parameter. In this method you can include your external libraries from [`src/public/lib`](../typescript-development/add-external-library.md), too.

```php
$this->enqueueReact(); // Ensures ReactJS v16.8 so hooks work
$this->enqueueMobx(); // Ensures MobX v4 (v5 needs proxies enabled in the browser)
```

This two methods allows you to ensure the correct version of **MobX** and **ReactJS** are enqueued.

#### Enqueue entrypoint

```php
$this->enqueueScript('admin', 'admin.js', $scriptDeps);
$this->enqueueStyle('admin', 'admin.css');
```

`enqueueScript()` and `enqueueStyle()` allows you to enqueue scripts and styles from your compiled TypeScript coding in [`src/public/{dist,dev}`](../typescript-development/using-entrypoints.md) depending on [`SCRIPT_DEBUG`](https://wordpress.org/support/article/debugging-in-wordpress/#script_debug).

{% hint style="info" %}
You can not enqueue `.ts` and `.scss` files because a browser does not understand it. The boilerplate automatically uses the **compiled** `.js` and `.css` files!
{% endhint %}

{% page-ref page="../typescript-development/using-entrypoints.md" %}

#### Enqueue external library

```php
$useNonMinifiedSources = $this->useNonMinifiedSources();
$this->enqueueLibraryScript("react", [
    [$useNonMinifiedSources, 'react/umd/react.development.js'],
    'react/umd/react.production.min.js'
]);
```

`enqueueLibraryScript()` and `enqueueLibraryStyle()` is similar to the above two methods except it enqueue files from [`src/public/lib`](../typescript-development/add-external-library.md) (**external libraries**). The second argument can be a plain `string` or in the above format: `array` with `string` or `string[]` items - so you can enqueue for example development files **depending on a condition**.

{% page-ref page="../typescript-development/add-external-library.md" %}

{% page-ref page="../advanced/create-package.md" %}

#### Pass variables to client-side

`overrideLocalizeScript($context)`: Localize the WordPress backend and frontend. If you want to provide URLs to the frontend you have to consider that some JS libraries do not support umlauts in their URI builder. For this you can use `base\Assets#getAsciiUrl`.

Also, if you want to use the options typed in your frontend you should adjust the following file too: [`src/public/ts/store/option.tsx`](../typescript-development/consume-php-variable.md#add-own-variable).

{% page-ref page="../typescript-development/consume-php-variable.md" %}

## Core

`Core.php`: Singleton core class handles the main system for plugin. It includes registering of the **autoloader**, all hooks (**actions & filters**). This singleton class can be get in each class with `$this->getCore()`.

`init()`: [Register all actions and filters](add-classes-hooks-libraries.md#add-new-hooks).

## Localization

`Localization.php`: i18n management for [backend ](localization.md)and [frontend](../typescript-development/localization.md).

`ooverride($locale)`: Put your language overrides here so you can maintain **one language for multiple regions**. Example:

```php
switch ($locale) {
    // Put your overrides here!
    case 'de_AT':
    case 'de_CH':
    case 'de_CH_informal':
    case 'de_DE_formal':
        return 'de_DE';
        break;
    default:
        break;
}
return $locale;
```

{% hint style="info" %}
Location overrides also affects client-side!
{% endhint %}

## Notice

There are two other classes you should consider to use:

-   `src/inc/base/UtilsProvider.php`: All of your classes should use this trait so composer packages can consume plugin relevant data.
-   `\MatthiasWeb\Utils\Service`: Static helper functions like REST Url getter, see also [this](example-implementations.md#rest-endpoint)

{% hint style="info" %}
It is recommend to not implement own coding into utils classes `packages/utils/src` by yourself. Imagine you want to upgrade to a newer version of WP ReactJS Starter you need to copy & paste all your customizations manually!
{% endhint %}
