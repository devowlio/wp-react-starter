# Create Add-On

What's an Add-On? In our boilerplate context an Add-On is a plugin **relying on another plugin**. That means, for example use PHP API functions from another plugin or connect to a MobX store of another plugin.

First of all, create a new plugin within your workspace:

```
create-wp-react-app create-plugin
```

You will get some prompts and afterwards follow the below steps.

## Link and usage

1. Run `yarn lerna add @wp-reactjs-multi-starter/wp-reactjs-starter --scope @wp-reactjs-multi-starter/my-addon` so `lerna` adds a plugin as dependency to the add-on.
1. Run `yarn lerna link` to link the plugins together
1. (optional) Add the entrypoint `wp-reactjs-starter-admin` to the `Assets` `$scriptDeps` array so the Add-On's `.js` is only enqueued when the other entrypoint is enqueued.
1. Navigate to your plugin which consumes the dependency and add the dependency path to [`.wprjss only changes`](../gitlab-integration/extend-gitlab-ci-pipeline.md#plugin)

```typescript
import { stores } from "@wp-reactjs-multi-starter/wp-reactjs-starter/src/public/ts/admin";

console.log("Stores from my parent plugin: ", stores);
```

{% hint style="success" %}
Wow! Additionally our [`webpack.factory.ts`](../usage/folder-structure/root.md#folder-structure) prevents bundling the plugins' code into the Add-On. That means, it is registered as external and directly uses their coding.
{% endhint %}

{% hint style="info" %}
You have to replace `wp-reactjs-multi-starter` and `wp-reactjs-starter` with your names.
{% endhint %}

## Provide public PHP API

If you aim to provide a public PHP API to your WordPress users you mostly create prefixed functions, for example `wp_rml_get_object()`. This is a recommend way but you should not create too much functions, furthermore work with factory functions and let the user work with class instances. Never mind, usually all PHP files in your plugin are scoped but there is one exception: `plugins/*/src/inc/api/**/*.php` files. Create all your public functions there and they will be available in the global scope.

## Deploy types to npmjs.com

Perhaps it can be interesting making types available to third-party developers so they are able to extend your plugin. For this, you need to do the following:

1. Remove `private` in your plugins' `package.json`
1. Run `tsc` in your plugin folder to generate `types` folder with `.d.ts` files
1. Repeat the two steps above for the `utils` package, too
1. Commit the files and the rest is doing `lerna` when merging to `master`

{% hint style="info" %}
Make sure you have configured the GitLab CI `NPM_TOKEN` variable with the [npm token](https://docs.npmjs.com/about-authentication-tokens). Learn more [here](../gitlab-integration/extend-gitlab-ci-pipeline.md#available-variables).
{% endhint %}
