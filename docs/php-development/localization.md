# Localization

There are two types of localization (i18n) coming with this boilerplate. In this part we explain the PHP localization. Just have a look at [`src/languages/your-plugin.pot`](../usage/folder-structure/plugin.md#folder-structure) - that's the main PHP i18n file.

To localize your plugin use the i18n functions coming with [**WordPress core**](https://developer.wordpress.org/plugins/internationalization/localization/): `__`, `_n` and so on ([difference](https://wpengineer.com/2237/whats-the-difference-between-__-_e-_x-and-_ex/)). The `.pot` file is automatically generated while using [`yarn docker:start`](../usage/available-commands/root.md#development). To generate the file manually you can use the command [`yarn i18n:generate:backend`](../usage/available-commands/plugin.md#localization) in your plugin folder.

{% hint style="info" %}
When using the above functions always use your own text domain constant `WPRJSS_TD` as context parameter.
{% endhint %}

{% page-ref page="../typescript-development/localization.md" %}
