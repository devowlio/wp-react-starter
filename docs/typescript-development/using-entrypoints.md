# Using entrypoints

If you get deeper into plugin development you will notice that some frontend coding is not needed for all pages. Some scenarios:

-   **Gutenberg block**: Only load JS / CSS when the Gutenberg block is loaded
-   **Shortcode**: Only load JS / CSS when the Gutenberg block is loaded
-   **Widget**: A "big" widget should outsource coding to its own entrypoint
-   **WP Admin page**: Each page should only load their JS / CSS

The boilerplate and example implementations use the simplest scenario and loads a single `admin.js` on `wp-admin` and `widget.js` on the website itself.

For this, you should consider **conditional loading** of JS / CSS files. Let's go with an example, which should only be loaded on `wp-admin/edit.php`. Now, how to create a **new entrypoint**? In our boilerplate context, an "entrypoint" is each file in [`plugins/your-plugin/src/public/ts/*`](../usage/folder-structure/plugin.md#folder-structure) (**first level, no sub folders**). Create a new file [`plugins/your-plugin/src/public/ts/edit.tsx`](../usage/folder-structure/plugin.md#folder-structure):

```text
console.log("I am only loaded in posts edit page!");
```

Afterwards run [`yarn docker:start`](../usage/available-commands/root.md#development) and the new entrypoint `edit.js` is compiled to [`src/public/{dev,dist}`](../usage/folder-structure/plugin.md#folder-structure). Open [`Assets#enqueue_scripts_and_styles`](../php-development/predefined-classes.md#enqueue-entrypoint) and add this:

```php
public function enqueue_scripts_and_styles($type) {
    if ($this->isScreenBase("edit")) {
        $this->enqueueScript(WPRJSS_SLUG . '-edit', 'edit.js');
    }
}
```

{% hint style="info" %}
If you can not use [`Assets#enqueue_scripts_and_styles`](../php-development/predefined-classes.md#enqueue-entrypoint) to enqueue your conditional files (e. g. Shortcode) you can always `$this->getCore()->getAssets()` to use the boilerplate enqueue methods.
{% endhint %}
