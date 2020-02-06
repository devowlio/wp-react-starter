# Package

Each package has a `package.json` with predefined scripts and commands. Use `cd packages/your-package` to navigate to your package so that you can execute the following commands.

All commands **marked with** 💡 are useful for **getting started**.

Useful to know: All [root commands](../available-commands/root.md) can also be used with `yarn root:run <your-command>` within any package directory.

{% page-ref page="../../advanced/create-package.md" %}

## Development

| Command             | Description                                                                                                                                                           |
| :------------------ | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 💡 `yarn bootstrap` | Install composer dependencies. This command is called automatically with the command root `yarn bootstrap`.                                                           |
| 💡 `yarn dev`       | Start watching TypeScript and PHP files and automatically compile distribution files. This command is called automatically with the command root `yarn docker:start`. |
| 💡 `yarn lint`      | Prints errors and warnings about coding styles (ESLint, PHPCS). This command is called automatically with the command root `yarn workspace:lint`.                     |
| `yarn lint:eslint`  | Prints errors and warnings about coding styles (ESLint).                                                                                                              |
| `yarn lint:phpcs`   | Prints errors and warnings about coding styles (PHPCS).                                                                                                               |

## Localization

| Command                       | Description                                                                        |
| :---------------------------- | :--------------------------------------------------------------------------------- |
| `yarn i18n:generate:backend`  | Generate `.pot` files from extracted localization strings of your backend coding.  |
| `yarn i18n:generate:frontend` | Generate `.pot` files from extracted localization strings of your frontend coding. |

{% page-ref page="../../php-development/localization.md" %}

{% page-ref page="../../typescript-development/localization.md" %}

## Build

| Command                     | Description                                                                                                                                                                                         |
| :-------------------------- | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 💡 `yarn build`             | Builds JavaScript development and production files.                                                                                                                                                 |
| `yarn build:js:production`  | Create a production build of React files. The files will be created in `dist`. These files should be loaded if `SCRIPT_DEBUG` is not active.                                                        |
| `yarn build:js:development` | Create a development build of React files. The files will be created in `dev`. These files should be loaded when `SCRIPT_DEBUG` is active.                                                          |
| `yarn build:webpack:done`   | This script is called automatically when the webpack is done with bundling (also in watch mode). So, after each build [localization](../../advanced/create-package.md#localization) is regenerated. |

## Tests

| Command                      | Description                                |
| :--------------------------- | :----------------------------------------- |
| `yarn test`                  | Run all tests.                             |
| 💡 `yarn test:phpunit`       | Run PHPUnit tests.                         |
| `yarn test:phpunit:coverage` | Run PHPUnit tests with coverage reporting. |
| 💡 `yarn test:jest`          | Run Jest tests.                            |
| `yarn test:jest:coverage`    | Run Jest tests with coverage reporting.    |

{% page-ref page="../../advanced/tests.md" %}

## Licenses

| Command                             | Description                                                                  |
| :---------------------------------- | :--------------------------------------------------------------------------- |
| `yarn grunt yarn:license:check`     | Check valid licenses of JavaScript production dependencies with `yarn`.      |
| `yarn grunt composer:license:check` | Check valid licenses of PHP production dependencies with `composer`.         |
| `yarn grunt yarn:disclaimer`        | Generate disclaimer file for JavaScript production dependencies with `yarn`. |
| `yarn grunt composer:disclaimer`    | Generate disclaimer file for PHP production dependencies with `composer`.    |

{% page-ref page="../../advanced/license-checker.md" %}

{% hint style="info" %}
There are other commands in `package.json` which are not worth mentioning here, as they are only used automatically in the CI/CD.
{% endhint %}
