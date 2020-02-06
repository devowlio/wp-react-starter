# Add external library

If you add a new dependency in your plugin with `yarn add` and use it with `import * from "anyhting"` it will be bundled automatically in the resulting `.js` file.

## Disadvantages

This can be a problem in matter of **SEO and performance**. The bundle size can be very big (KiB, MiB) and as we all know it affects the page loading. Also, WordPress is a "modular" and high-extensible CMS (is it a CMS?) - Plugins and Themes bundles a lot of external libraries, too. So, why loading the content of a library twice?

## Solution

The boilerplate comes out-of-the-box with a mechanism to automatically serve library files correctly. Enqueue the library files using the predefined [`Assets`](../php-development/predefined-classes.md#enqueue-external-library) class. Following we explain the usage by an example:

-   **1.** Add your dependency with `yarn add`, e. g. `yarn add sortablejs`
-   **2.** Navigate to `node_modules/sortablejs`
-   **3.** The most libraries provide a CommonJS or UMD module. So, get the pathes to that files (if possible, also search for development files - mostly they do not contain `.min.js`)
-   **4.** Open [`plugins/your-plugin/scripts/Gruntfile.ts`](../usage/folder-structure/plugin.md#folder-structure) and add `sortablejs` to `clean:npmLibs`

```typescript
clean: {
    npmLibs: {
        // [...]
        src: [
            /* [...] */
            "sortablejs" // <---
        ];
    }
}
```

-   **5.** Add the found module files to `copy:npmLibs`

```typescript
copy: {
    npmLibs: {
        // [...]
        src: [
            // [...]
            "sortablejs/*.js" // <---
        ];
    }
}
```

-   **6.** If the module files are minified and contain Sourcemap files `.js.map`, also add the module files to `strip_code:sourcemaps`:

```typescript
strip_code: {
    sourcemaps: {
        // [...]
        src: [
            // [...]
            "sortablejs/Sortable.min.js" // <---
        ];
    }
}
```

-   **7.** Run [`yarn grunt libs:copy`](../usage/available-commands/plugin.md#development) so all library files gets copied to [`plugins/your-plugin/src/public/lib`](../usage/folder-structure/plugin.md#folder-structure)\`\`
-   **8.** Open your [`Assets#enqueue_scripts_and_styles`](../php-development/predefined-classes.md#enqueue-external-library) class and enqueue the library scripts and styles:

```php
// .js
$useNonMinifiedSources = $this->useNonMinifiedSources();
$this->enqueueLibraryScript("sortablejs", [
    [$useNonMinifiedSources, 'sortablejs/Sortable.js'],
    'sortablejs/Sortable.min.js'
]);
```

-   **9.** If you have a look at your browser network log you see that the plugin automatically appends the right module version to the resource URL due to the cachebuster mechanism
-   **10.** The library is now automatically copied, loaded and served on your WordPress site. The last thing is telling webpack not bundling that package into the compiled `.js` files - instead it should refer to `window.Sortable`. For this, open [`plugins/your-plugin/scripts/webpack.config.ts`](../advanced/extend-compose-webpack.md#webpack):

```typescript
import { createDefaultSettings } from "../../../common/webpack.factory";

export default createDefaultSettings((settings) => {
    Object.assign(settings.externals, {
        sortablejs: "Sortable"
    });
});
```

{% hint style="success" %}
You can use the library without limitations in your TypeScript files:
{% endhint %}

```typescript
import Sortable from "sortablejs";
```

{% hint style="warning" %}
Where do I know `window.Sortable`? Well, it is the exported name of the library. Most libraries provide such a name but there are also exceptions - then you must skip the `Gruntfile.js` modification and directly bundle it to your compiled `.js` files.
{% endhint %}

{% hint style="info" %}
This seems to be a lot of work, but if you have made this twice you feel like "Wow, it's very easy and it works pretty" - and you never need the introduction above again. The above example does not include a `.css` but it can also be copied like this and enqueued with [`enqueueLibraryStyle()`](../php-development/predefined-classes.md#enqueue-external-library).
{% endhint %}

{% page-ref page="../advanced/extend-compose-webpack.md" %}
