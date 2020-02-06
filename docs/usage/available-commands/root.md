# Root

The root `package.json` provides scripts and commands you can use for **fast and efficient development**. Additionally you can use all the available commands of [`lerna`](https://github.com/lerna/lerna) for the monorepo management.

All commands **prefixed** with 💡 are useful for **getting started**.

## Development

| Command                 | Description                                                                                                                                                                            |
| :---------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 💡 `yarn bootstrap`     | Downloads all dependencies, installs and links them together. Also runs `bootstrap` of all your subpackages if available. This is useful if a teammate has checked out the repository. |
| 💡 `yarn docker:start`  | Start Docker containers locally. Each package will run `yarn dev` concurrently (all of your plugins are available in the WordPress Docker container).                                  |
| 💡 `yarn docker:stop`   | Stop Docker containers.                                                                                                                                                                |
| 💡 `yarn docker:rm`     | Remove Docker containers.                                                                                                                                                              |
| 💡 `yarn docker:purge`  | Remove all Docker containers and purge the volumes additionally so that you can start from scratch.                                                                                    |
| `yarn wp-cli <command>` | Run a WP-CLI command within the WordPress Docker container, for example `yarn wp-cli "wp core version"`. Use `--silent` to suppress the output via `yarn`.                             |

{% page-ref page="../../advanced/extend-compose-webpack.md" %}

## Debugging

| Command                   | Description                                                                                                                             |
| :------------------------ | :-------------------------------------------------------------------------------------------------------------------------------------- |
| `yarn debug:php:generate` | Generate a valid `launch.json` file to respect all available packages and plugins.                                                      |
| `yarn debug:php:start`    | Start XDebug in WordPress container. You do not have to manually call this in VSCode, because it is automatically run through debugger. |
| `yarn debug:php:stop`     | Stop XDebug in WordPress container. You do not have to manually call this in VSCode, because it is automatically run through debugger.  |

{% page-ref page="../../php-development/debugging.md" %}

## Database

| Command                              | Description                                                                                                                                   |
| :----------------------------------- | :-------------------------------------------------------------------------------------------------------------------------------------------- |
| `yarn db:snapshot-import-on-startup` | Take a snapshot of the current database tables and save it so that the next WordPress installation automatically imports this snapshot.       |
| `yarn db:snapshot <file>`            | Creates a snapshot of the current database tables and a save dump to the specified file.                                                      |
| `yarn db:snapshot-import`            | The installation snapshot created with `yarn db:snapshot-import-on-startup` is imported into the currently running Docker WordPress instance. |

{% page-ref page="../../advanced/persistent-database-snapshot.md" %}

## Misc

| Command                                                    | Description                                                                                                                                                |
| :--------------------------------------------------------- | :--------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 💡 `yarn docker-compose <arguments>`                       | Run `docker-compose` within the project. Do not use `docker-compose` directly, as the boilerplate provides additional configurations for `docker-compose`. |
| `DCOMPOSE_SERVICE_NAME=<service> yarn docker-compose:name` | Get the name of a dynamically generated container for a given service name.                                                                                |
| `yarn docker-compose:name:wordpress`                       | Get the dynamically generated container name of `wordpress` service.                                                                                       |
| `yarn wp-wait`                                             | Wait for the WordPress container is up and running, afterwards continue script execution.                                                                  |

## Workspace

All root commands starting with `workspace:` runs for all your subpackages in your multi-package repository. That includes packages in `packages/*` and `plugins/*`.

| Command                                                   | Description                                                                                                              |
| :-------------------------------------------------------- | :----------------------------------------------------------------------------------------------------------------------- |
| `yarn workspace:lint`                                     | Lint complete project including packages.                                                                                |
| `WORKSPACE_COMMAND=<command> yarn workspace:concurrently` | Run a command in all available packages concurrently.                                                                    |
| `COMPOSER_CONTEXT=<context> yarn workspace:compose-files` | Get absolute paths of all `**/devops/docker-compose/docker-compose.yml` files for a given context (`traefik|local|e2e`). |
| `yarn workspace:slugs`                                    | Get all plugin slugs whitespace separated.                                                                               |

{% hint style="info" %}
There are other commands in `package.json` which are not worth mentioning here, as they are only used automatically in the CI/CD.
{% endhint %}
