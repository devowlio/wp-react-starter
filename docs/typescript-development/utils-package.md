# Utils package

As we are using a **multi-package-/mono repository** we have the ability and advantage of **modular** package **development**. That means, coding which should be used in multiple plugins can be **outsourced** to an own package. This can also be done for PHP ([`packages/utils/src`](../usage/folder-structure/root.md#folder-structure)), but this does not matter here.

{% page-ref page="../advanced/create-package.md" %}

With [`create-wp-react-app create-workspace`](../usage/getting-started.md#create-workspace) a main utils package in [`packages/utils`](../usage/folder-structure/root.md#folder-structure) is generated automatically.

## Factories

All of your plugins can use the factories defined in [`packages/utils/lib/factory`](../usage/folder-structure/root.md#folder-structure). They do a lot of work for you and implement a high standard for the following topics:

-   AJAX / `XMLHttpRequest` handler with predefined interfaces so all your WP REST API endpoints are typed, see also [this](example-implementations.md#rest-endpoint).
-   ReactJS [context](https://reactjs.org/docs/context.html) creation with a single function. That context must be used together with React [hooks](https://reactjs.org/docs/hooks-intro.html).
-   [Localization / i18n](localization.md#localize-typescript)
-   Object-orientated class to [consume options](consume-php-variable.md#predefined-variables) coming from a localized PHP variable.

{% hint style="info" %}
You do not need to call that factories manually because they are consumed automatically in your generated plugin file [`plugins/your-plugin/src/public/ts/utils/index.tsx`](../usage/folder-structure/plugin.md#folder-structure). Just use them.

It is recommend to not extend that utils package by yourself. Imagine you want to upgrade to a newer version of WP ReactJS Starter you need to copy & paste all your customizations manually. Create another package!
{% endhint %}
