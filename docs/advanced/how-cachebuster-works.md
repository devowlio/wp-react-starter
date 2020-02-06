# How cachebuster works

What is cachebusting? Simply said, its a dynamic `GET` parameter added to your `.js` or `.css` file so the browser is **forced to refetch** the resource when there is a known change.

The [`Assets`](../php-development/predefined-classes.md#assets) class provides a few scenarios of cachebusting when enqueueing scripts and styles.

## NPM Dependency

1. Adding a dependency to `package.json` (using `yarn add`)
1. Copy to `plugins/your-plugin/src/public/lib/` (using [`yarn grunt libs:copy`](../usage/available-commands/plugin.md#development))
1. Use [`Assets#enqueueLibraryScript()`](../php-development/predefined-classes.md#enqueue-external-library) to enqueue the handle

{% hint style="success" %}
The cachebuster is applied with the package version of the dependency.
{% endhint %}

{% page-ref page="../typescript-development/add-external-library.md" %}

## Entrypoints

1. Start developing on your entrypoints with [`yarn docker:start`](../usage/available-commands/root.md#development)\`\`
1. Change some code and webpack will transform the entrypoints automatically to production / development code
1. Use [`Assets#enqueueScript()`](../php-development/predefined-classes.md#enqueue-entrypoint) to enqueue the handle

{% hint style="success" %}
The cachebuster is applied with a hash (hash comes from [webpack](https://webpack.js.org/configuration/output/#outputhashdigest)).
{% endhint %}

{% page-ref page="../typescript-development/using-entrypoints.md" %}

## Unknown resources

You want to use a JavaScript library which is not installable through `yarn`

1. Put the files manually to [`plugins/your-plugin/src/public/lib`](../usage/folder-structure/plugin.md#folder-structure)\`\`
1. Use [`Assets#enqueLibraryScript()`](../php-development/predefined-classes.md#enqueue-external-library) (or `wp_enqueue_script` directly) to enqueue the handle

{% hint style="success" %}
The cachebuster is applied with your plugins' version.
{% endhint %}
