# Consume PHP variable

Sometimes it is needed to consume PHP variables in your frontend TypeScript coding. You are covered! The boilerplate comes with a mechanism to get a typed object.

## Predefined variables

If you have a look at [`src/public/ts/store/option.tsx`](../usage/folder-structure/plugin.md#folder-structure) you will find a typed `OptionStore`. You also notice that it extends from `BaseOptions`. The boilerplate comes out-of-the-box with important variables you can already use:

```typescript
public slug: string; // Plugin slug
public textDomain: string; // Plugin text domain, needed for i18n factory
public version: string; // Plugin version
public restUrl?: string; // REST API Url
public restNamespace?: string; // Plugin REST API namespace
public restRoot?: string; // REST API root path
public restQuery?: {}; // REST API query sent with each request to the backend (GET)
public restNonce?: string; // REST API authentication nonce
public publicUrl?: string; // Public url localhost:{your-port}/wp-content/plugins/your-plugin/public
```

## Access variables

The `OptionStore` can be used by React in that way (it relies to the context factory):

```typescript
() => {
    const { optionStore } = useStores();
    return <span>{optionStore.slug}</span>;
};
```

It can also read directly (relies on the root store [`src/public/ts/store/stores.tsx`](../usage/folder-structure/plugin.md#folder-structure)):

```typescript
console.log(rootStore.optionStore.slug);
```

## Add own variable

Assume we want to know if the user is allowed to install plugins (`install_plugins`). Adjust the [`Assets#overrideLocalizeScript()`](../php-development/predefined-classes.md#pass-variables-to-client-side) method and additionally make the variable only be exposed for a given context (site, admin):

```php
public function overrideLocalizeScript($context) {
    if ($context === base\Assets::TYPE_ADMIN) {
        return [
            'canInstallPlugins' => current_user_can('install_plugins')
        ];
    } elseif ($context === base\Assets::TYPE_FRONTEND) {
        // [...]
    }

    return [];
}
```

To make it available in TypeScript we need to adjust the `OptionStore#others` property:

```typescript
class OptionStore extends BaseOptions {
    // [...]

    @observable
    public others: { canInstallPlugins: boolean } = {
        // Defaults (optional)
        canInstallPlugins = false
    };
}
```

Let's access it:

```typescript
console.log(rootStore.optionStore.others.canInstallPlugins);
```
