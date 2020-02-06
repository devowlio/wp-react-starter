# Extend Compose and Webpack

You are an advanced Docker Compose and Webpack user? Then heads up, read on how to extend the boilerplate.

## Docker Compose

The `docker-compose.yml` file from [`devops/docker-compose/`](../usage/folder-structure/root.md#folder-structure) is deep merged with all found `{plugins/packages}/*/devops/docker-compose/docker-compose.yml` files. So, it is pretty easy to add a plugin specific container.

To see which `docker-compose.yml` files are merged, run the root command [`yarn workspace:compose-files`](../usage/available-commands/root.md#workspace).

To see the result of the merged `docker-compose.yml` file, you can run [`yarn docker-compose config`](../usage/available-commands/root.md#misc).

## Webpack

Webpack provides a lot of [configurations](https://webpack.js.org/configuration/). We do not prevent you from using them together with [`webpack.factory.ts`](../usage/folder-structure/root.md#folder-structure). Simply pass a callback to the factory and do what you want in [`plugins/your-plugin/scripts/webpack.config.ts`](../usage/folder-structure/plugin.md#folder-structure):

```typescript
import { createDefaultSettings } from "../../../common/webpack.factory";

export default createDefaultSettings("plugin", {
    override: (settings) => {
        // Do something with settings.
    }
});
```
