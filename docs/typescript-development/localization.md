# Localization

## Localize TypeScript

There are two types of localization (i18n) coming with this boilerplate. In this part we explain the TypeScript localization. Just have a look at [`src/public/languages/your-plugin.pot`](../usage/folder-structure/plugin.md#folder-structure) - that's the main TypeScript i18n file.

To localize your plugin use the i18n functions coming through the utils package: `__`, `_n` and so on. Under the hood [@wordpress/i18n](https://www.npmjs.com/package/@wordpress/i18n) is used. The `.pot` file is **automatically generated** when using [`yarn docker:start`](../usage/available-commands/root.md#development) in plugin. To generate the file manually you can use the command [`yarn i18n:generate:frontend`](../usage/available-commands/plugin.md#localization) in your plugin folder.

{% hint style="info" %}
When using the above functions it differs from the [PHP localization](../php-development/localization.md). You do not need to pass any context parameter because it is automatically respected through the factory function from the utils package.
{% endhint %}

## Interpolation

To explain "Interpolation" as best, follow below example. Before writing the following ReactJS code:

```javascript
// [...]
() => (
    <div>
        Hi{" "}
        <a title="This is your name." href="/user/bob">
            bob.
        </a>
        !
    </div>
);
// [...]
```

... you should use the `_i` function coming from the [utils package factory](utils-package.md#factories):

```javascript
const username = "bob";

// [...]
() =>
    _i(
        // Translate parameters into the string
        __("Hi {{a}}%(username)s{{/a}}!", {
            username: bob
        }),
        // Translate components
        {
            a: <a ttitle={__("This is your name.")} href={`user/${username}`} />
        }
    );
// [...]
```

What does this solve? Translating a HTML string does not work because ReactJS can not and **should not** recognize this automatically (for security reasons). Under the hood, [i18n-calypso](https://www.npmjs.com/package/i18n-calypso) is used.

{% hint style="success" %}
**Awesome**! Do no longer work with i18n keys in your frontend!.
{% endhint %}
