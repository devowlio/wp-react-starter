# Tests

Writing tests can be boring and lazy. But we promise, cover your plugin with tests for quality - we have covered you with the complete facility and configuration.

Follow this best practices to write good and maintainable tests:

-   [Writing Great Unit Tests: Best and Worst Practices](https://blog.stevensanderson.com/2009/08/24/writing-great-unit-tests-best-and-worst-practises/)
-   [The Front-End Test Pyramid: How to Rethink Your Testing](https://www.freecodecamp.org/news/the-front-end-test-pyramid-rethink-your-testing-3b343c2bca51/)
-   [Introduction to Front-End unit testing](https://dev.to/christopherkade/introduction-to-front-end-unit-testing-510n)
-   [A guide to unit testing in JavaScript](https://github.com/mawrkus/js-unit-testing-guide)
-   [You Still Don’t Know How to Do Unit Testing (and Your Secret is Safe with Me)](https://stackify.com/unit-testing-basics-best-practices/)
-   [Top 5 Cucumber Best Practices](https://blog.codeship.com/cucumber-best-practices/)

## Jest

_Unit- and snapshot testing_

Each packages and plugins comes with a predefined and bullet-proof TypeScript implementation of [Jest](https://jestjs.io/). Simply navigate to your package or plugin and run [`yarn test:jest`](../usage/available-commands/plugins.md#tests). Test files are located in [`{packages,plugins}/*/test/jest`](../usage/folder-structure/plugin.md#folder-structure).

If you have a look at the `jest.setup.js` file you will notice an inclusion of [`enzyme`](https://github.com/airbnb/enzyme). Enzyme is a JavaScript library for React testing utilities - and is preinstalled ad configured in all your future packages.

**Pro tip**: If you write Jest tests you can start watching test files with [`yarn jest --watch`](https://jestjs.io/docs/en/cli#--watch).

## PHPUnit

_Unit testing_

Each packages and plugins comes with a predefined and bullet-proof implementation of [PHPUnit](https://phpunit.de/). Simply navigate to your package or plugin and run [`yarn test:phpunit`](../usage/available-commands/plugins.md#tests). Test files are located in [`{packages,plugins}/*/test/phpunit`](../usage/folder-structure/plugin.md#folder-structure).

Unfortunately PHPUnit is not as handy as Jest, so we need to rely on further tools:

-   [**WP_Mock**](https://github.com/10up/wp_mock) is a WordPress API Mocking framework
-   [**WordPress Stubs**](https://github.com/php-stubs/wordpress-stubs) is needed to mock WordPress API functions and classes - brings also the advantage of VSCode autocompletion for WordPress resources
-   [**Mockery**](https://github.com/mockery/mockery) is the player in PHPUnit testing frameworks and provides a lot of useful functionality to mock your source code in tests
-   [**Patchwork**](http://patchwork2.org/api/) is an awesome tool to mock PHP internal functions as this is not possible by default

The boilerplate provides a `TestCaseUtils` trait in [`common/phpunit.base.php`](../usage/folder-structure/plugin.md#folder-structure) file. If you use that trait in your Test classes you can abstract util functionalities. You will find a predefined `expectCallbacksReached` method allowing you to expect a given callback is reached - it plays well with `redefine` (Patchwork):

```php
<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use TestCaseUtils;
use WP_Mock\Tools\TestCase;

use function Patchwork\redefine;

final class MyTest extends TestCase
{
    use TestCaseUtils;

    public function testErrorLogWrites()
    {
        $this->expectCallbacksReached(['errorLog']);

        redefine('error_log', function ($callback) {
            $this->addCallbackReached('errorLog');
        });

        error_log("Something");

        $this->assertCallbacksReached();
    }
}
```

**Pro tip**: If you write PHPUnit tests you can focus on single tests with [`yarn phpunit --filter testErrorLogWrites$`](https://phpunit.readthedocs.io/en/8.5/textui.html).

## Coverage

For both Jest and PHPUnit you can collect coverage information, for this refer to [`yarn test:{jest|phpunit}:coverage`](../usage/available-commands/plugins.md#tests). If you commit to GitLab repository and start a merge request you will notice a `Code coverage` percent right to your merge request (as you can see [here](https://docs.gitlab.com/ee/user/project/pipelines/settings.html#test-coverage-parsing)).

Also, [codecov.io](https://codecov.io/) is fully implemented if you just add an environment variable [`CODECOV_TOKEN`](../gitlab-integration/extend-gitlab-ci-pipeline.md#available-variables). After you have setup Codecov you will get code coverage reports in your GitLab repository merge requests.

{% hint style="info" %}
Jest and PHPUnit tests coming with the boilerplate does not covered example implementations as they are removed the most time after plugin creation.
{% endhint %}

With coverage reports you will hear also about **Coverage threshold**. It means, if you do not reach a given percentage of coverage the pipeline will fail and you are not able to merge. The thresholds are configured in [`{plugins,packages}/*/package.json#phpunit-coverage-threshold`](../usage/folder-structure/plugin.md#folder-structure) and [`common/jest.base.js#coverageThreshold`](../usage/folder-structure/root.md#folder-structure). The predefined threshold is `80`.

{% hint style="warning" %}
If you want to **locally** collect coverage reports for your PHPUnit tests (`yarn test:phpunit:coverage`) you need to install [XDebug](https://xdebug.org/docs/install).
{% endhint %}

## E2E

_Integration- and End-to-end testing for plugins_

E2E are tests which runs directly in a (headless) browser and **simulates a real user**. The boilerplate comes with [Cypress](https://www.cypress.io/) - currently the best E2E solution we know - together with [Cucumber](https://github.com/TheBrainFamily/cypress-cucumber-preprocessor#readme) (a [Gherkin](https://cucumber.io/docs/gherkin/) syntax implementation for Cypress).

You can find the cypress related files (_user interactions_) in [`plugins/your-plugin/test/cypress/`](../usage/folder-structure/plugin.md#folder-structure). The example implementation simply logs into WordPress and checks if the [Hello World REST API](../php-development/example-implementations.md#rest-endpoint) endpoint is available, and adds a Todo item. When opening the folder you will notice the following important abstraction:

-   📁 `integration`
    -   📄 `adminPage.feature` Describing your `.feature` files in [Gherkin syntax](https://github.com/TheBrainFamily/cypress-cucumber-preprocessor#single-feature-files)
-   📁 `step-definitions` Represents your `.feature` implementations
    -   📁 `adminPage`
        -   📄 `adminPage.ts` Example E2E test for the WordPress [admin page](../php-development/example-implementations.md#menu-page)
        -   📄 `AdminPageObject.ts` An page object describes a single page and exposes static methods. Generally you maintain all your "CSS" selectors there
    -   📁 `common`
        -   📄 `common.ts` _Shareable_ expressions you can use in your `.feature` files

With GitLab CI, Cypress brings a lot of cool features to you: Do E2E tests for each commit in a complete fresh WordPress instance and make a video if something goes wrong. Prerequisite is an own GitLab runner.

**Pro tip**: If you write E2E tests you can focus on single features e. g. with `yarn cypress-tags run -e TAGS='@only'`. Refer to [Running tagged tests](https://github.com/TheBrainFamily/cypress-cucumber-preprocessor#running-tagged-tests).

{% hint style="info" %}
Do **not** add configurations to `cypress.josn` file **directly**. Open [`plugins/your-plugin/test/cypress/plugins/index.js`](../usage/folder-structure/plugin.md#folder-structure) and have a look at the `applyConfig` function.
{% endhint %}
