# Example implementations

The PHP server-side comes with some example implementations so you can better understand how things work.

All the example implementations contain a client-side (TypeScript) implementation, too:

{% page-ref page="../typescript-development/example-implementations.md" %}

## Menu page

When [opening your WordPress](../usage/getting-started.md#open-wordpress) `wp-admin` and activate the plugin, you will notice a new page on the left sidebar with the name of your plugin. This example is located in [`src/inc/view/menu/Page.php`](../usage/folder-structure/plugin.md#folder-structure) and initialized through [`Core`](predefined-classes.md#core).

Additionally it enqueues the following scripts in [`Assets`](predefined-classes.md#enqueue-entrypoint):

```php
$handle = WPRJSS_SLUG . '-admin';
$this->enqueueScript($handle, 'admin.js', $scriptDeps);
$this->enqueueStyle($handle, 'admin.css');
```

Following, the TypeScript entrypoint from [`src/public/ts/admin.tsx`](../typescript-development/using-entrypoints.md) is executed when visiting that page. Opening that menu page, some notices and a todo list is visible (under the scenes it uses a MobX store). Additionally the [Hello World REST](example-implementations.md#rest-endpoint) endpoint ([`src/inc/rest/HelloWorld.php`](../usage/folder-structure/plugin.md#folder-structure)) can be requested through a link. See also [this](../typescript-development/example-implementations.md#menu-page).

## Widget

In WordPress widgets section you should also notice a new widget "React Demo Widget". If a page contains the widget, the TypeScript coding from [`src/public/ts/widget.tsx`](../usage/folder-structure/plugin.md#folder-structure) is executed. It simply shows a "Hello World" ReactJS component.

The widget is initialized through [`Core#widgets_init`](predefined-classes.md#core) and enqueued in [`Assets`](predefined-classes.md#enqueue-entrypoint). See also [this](../typescript-development/example-implementations.md#widget).

## REST endpoint

All your REST API endpoints should be put into [`src/inc/rest`](../usage/folder-structure/plugin.md#folder-structure). A Hello World endpoint is implemented in [`src/inc/rest/HelloWorld.php`](../usage/folder-structure/plugin.md#folder-structure). It follows the standard WP REST API specification defined [here](https://developer.wordpress.org/rest-api/).

So, if you request a `GET` (simply open in web browser) of `localhost:{your-port}/wp-json/your-plugin/hello-world` you will get a response. See also [this](../typescript-development/example-implementations.md#rest-endpoint).

{% hint style="warning" %}
The API endpoints does not automatically ensure a logged-in user because it relies not on the current logged in user. It is handled through the so-called `_wpnonce`. Read more about it [here](https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/).
{% endhint %}
