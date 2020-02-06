# Plugin

Each plugin has a `package.json` with predefined scripts and commands. Use `cd plugins/your-plugin` to navigate to your plugin so that you can execute the following commands.

All commands **marked with** 💡 are useful for **getting started**.

Useful to know: All [root commands](../available-commands/root.md) can also be used with `yarn root:run <your-command>` within any plugin directory.

## Development

| Command                       | Description                                                                                                                                                            |
| :---------------------------- | :--------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 💡 `yarn bootstrap`           | Install composer dependencies, copy defined libraries, and create an initial cachebuster. This command is called automatically with the command root `yarn bootstrap`. |
| 💡 `yarn dev`                 | Start watching TypeScript and PHP files and automatically compile distribution files. This command is called automatically with the command root `yarn docker:start`.  |
| 💡 `yarn lint`                | Prints errors and warnings about coding styles (ESLint). This command is called automatically with the command root `yarn workspace:lint`.                             |
| `yarn grunt libs:copy`        | Copy libraries defined in `scripts/Gruntfile.ts` to `src/public/lib`.                                                                                                  |
| `yarn grunt libs:cachebuster` | Generate cachebuster in `src/inc/base/others/`.                                                                                                                        |
| `yarn lint:eslint`            | Prints errors and warnings about coding styles (ESLint).                                                                                                               |
| `yarn lint:phpcs`             | Prints errors and warnings about coding styles (PHPCS).                                                                                                                |

{% page-ref page="../../typescript-development/add-external-library.md" %}

{% page-ref page="../../advanced/how-cachebuster-works.md" %}

## Localization

| Command                       | Description                                                                        |
| :---------------------------- | :--------------------------------------------------------------------------------- |
| `yarn i18n:generate:backend`  | Generate `.pot` files from extracted localization strings of your backend coding.  |
| `yarn i18n:generate:frontend` | Generate `.pot` files from extracted localization strings of your frontend coding. |

{% page-ref page="../../php-development/localization.md" %}

{% page-ref page="../../typescript-development/localization.md" %}

## Build

| Command                     | Description                                                                                                                                                                                                                                                   |
| :-------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| 💡 `yarn build`             | Bundles all plugin files and puts them into the "build" folder. This folder can be uploaded to wordpress.org, for example. Usually the build and release process is done by the GitLab CI.                                                                    |
| `yarn build:js:production`  | Create a production build of React files. The files will be created in `src/public/dist`. These files should be loaded if `SCRIPT_DEBUG` is not active.                                                                                                       |
| `yarn build:js:development` | Create a development build of React files. The files will be created in `src/public/dev`. These files should be loaded when `SCRIPT_DEBUG` is active.                                                                                                         |
| `yarn build:webpack:done`   | This script is called automatically when the webpack is done with bundling (also in watch mode). So, after each build [cachebuster](../../advanced/how-cachebuster-works.md) and [localization](../../typescript-development/localization.md) is regenerated. |

{% page-ref page="../../advanced/extend-compose-webpack.md" %}

{% page-ref page="../../advanced/build-production-plugin.md" %}

## Tests

| Command                      | Description                                |
| :--------------------------- | :----------------------------------------- |
| `yarn test`                  | Run all tests (expect E2E).                |
| 💡 `yarn test:phpunit`       | Run PHPUnit tests.                         |
| `yarn test:phpunit:coverage` | Run PHPUnit tests with coverage reporting. |
| 💡 `yarn test:jest`          | Run Jest tests.                            |
| `yarn test:jest:coverage`    | Run Jest tests with coverage reporting.    |
| 💡 `yarn test:cypress`       | Run Cypress E2E tests headless.            |
| `yarn cypress open`          | Open Cypress GUI runner.                   |

{% page-ref page="../../advanced/tests.md" %}

## Documentation

| Command           | Description                                       |
| :---------------- | :------------------------------------------------ |
| 💡`yarn docs`     | Generate all documents at once.                   |
| `yarn docs:php`   | Generate PHP docs in `docs/php`.                  |
| `yarn docs:js`    | Generate TS (JS) docs in `docs/js`.               |
| `yarn docs:api`   | Generate API docs in `docs/api`.                  |
| `yarn docs:hooks` | Generate Actions and Filters docs in `docs/hook`. |

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
