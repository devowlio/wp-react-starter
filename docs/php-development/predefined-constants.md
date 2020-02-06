# Predefined constants

Do you know [PHP constants](https://www.php.net/manual/language.constants.php)? They can be really awesome so you do not need to repeat strings and configurations. The boilerplate comes with a set of predefined constants you simply need to consume. They can be found in [`plugins/your-plugin/src/index.php`](../usage/folder-structure/plugin.md#folder-structure).

{% hint style="info" %}
In this table the constants are prefixed with `WPRJSS`, but you need to use your constants prefix you used in [`create-wp-react-app create-plugin`](../usage/getting-started.md#create-workspace).
{% endhint %}

| Constant                | Description                                                                                                                                                                      |
| :---------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `WPRJSS_FILE`           | Plugin file (`__FILE__`).                                                                                                                                                        |
| `WPRJSS_PATH`           | Plugin path (`dirname(__FILE__)`).                                                                                                                                               |
| `WPRJSS_SLUG`           | Plugin slug.                                                                                                                                                                     |
| `WPRJSS_INC`            | PHP include path (`inc/`).                                                                                                                                                       |
| `WPRJSS_MIN_PHP`        | Minimum PHP version needed for the plugin.                                                                                                                                       |
| `WPRJSS_MIN_WP`         | Minimum WP version needed for the plugin.                                                                                                                                        |
| `WPRJSS_NS`             | PHP namespace as string.                                                                                                                                                         |
| `WPRJSS_DB_PREFIX`      | Database table prefix. Should be used for example in [`dbDelta`](https://developer.wordpress.org/reference/functions/dbdelta/) ([`Activator`](predefined-classes.md#activator)). |
| `WPRJSS_OPT_PREFIX`     | Option prefix. Should be used for example in [`update_option`](https://developer.wordpress.org/reference/functions/update_option/).                                              |
| `WPRJSS_SLUG_CAMELCASE` | Plugin slug in camel case - used for frontend coding.                                                                                                                            |
| `WPRJSS_TD`             | Text domain. Should be used for example in [`__`](https://developer.wordpress.org/reference/functions/__/).                                                                      |
| `WPRJSS_VERSION`        | Plugin version.                                                                                                                                                                  |
| `WPRJSS_DEBUG`          | If `true`, `$this->debug()` outputs the log to `error_log`.                                                                                                                      |
