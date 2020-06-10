# Getting started

## Prerequisites

Please make sure that the following components are installed on your local computer:

-   [**PHP**](https://www.php.net/manual/en/install.unix.debian.php): PHP runtime (version >= 7.2 and < 7.4 with modules `common`, `cli`, `mbstring`, `json`, `xml` and `xdebug`)
-   [**Node.js**](https://nodejs.org/): JavaScript runtime (version >= 10.17)
-   [**Yarn**](https://yarnpkg.com/lang/en/): Dependency manager for JavaScript (version >= 1.19)
-   [**Composer**](https://getcomposer.org/): Dependency manager for PHP (version >= 1.9)
-   [**Prestissimo**](https://packagist.org/packages/hirak/prestissimo): Paralelized dependency installation for Composer (version >= 0.3.10)
-   [**Docker**](https://docs.docker.com/install/): Container Platform CLI `docker` and `docker-compose` (Docker Desktop version >= 2.1)
-   [**WP-CLI**](https://wp-cli.org/#installing): CLI for for WordPress `wp`
-   [**GitLab**](https://gitlab.com): Sign up for your Git repository and make predefined [CI/CD functionality](../gitlab-integration/predefined-pipeline.md) work
-   (optional) [**Visual Studio Code**](https://code.visualstudio.com/): IDE with the best integration for tools used in `wp-react-starter`

{% hint style="warning" %}
The boilerplate is **not** yet fully supported on Windows systems. Feel free to contribute or use a [Windows Subsystem 2 for Linux (WSL2)](https://docs.microsoft.com/windows/wsl/wsl2-install).
{% endhint %}

## Installation

[**`create-wp-react-app`**](https://github.com/devowlio/create-wp-react-app) allows you to create workspaces, plugins and packages with a single command. Let's install the CLI:

```bash
$ yarn global add create-wp-react-app
```

Run this command to install `create-wp-react-app` globally on your system. Afterwards you can then create your first workspace (in this context, the workspace means the folder where your multipackage repository is stored).

## Create workspace

```bash
$ create-wp-react-app create-workspace
```

Use this command to create the workspace for your WordPress plugin development. This command also executes the `create-wp-react-app create-plugin` command to create your first plugin. You will be asked to fill in the necessary information about your workspace and the first plugin. Once you have completed the prompts, the command runs the setup and prepares the repository.

{% hint style="success" %}
**Awesome!** You have created your first workspace and your first plugin. You can create as many plugins as you want in one workspace. If you are using the GitLab CI the **first** pipeline execution can take a while.
{% endhint %}

{% hint style="warning" %}
Do you get an error while bootstrap, docker initialization or something else? Do not worry, the main issue are missing dependencies. Just navigate to the created workspace with `cd your-workspace` and rerun `yarn bootstrap` and `yarn docker:start` - after you have installed the missing dependencies.
{% endhint %}

You can find out how to create additional plugins or packages in further articles:

{% page-ref page="../advanced/create-package.md" %}

{% page-ref page="../advanced/create-add-on.md" %}

## Remote development

If you are developing on a remote system and using VSCode already, you need to use the [Remote SSH extension](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-ssh). Simply connect to your server and install all required VSCode extensions. Navigate to your workspace folder and create a new `.env` file with the following content:

```
WP_LOCAL_INSTALL_URL=http://<your-ip>:<your-port>/
```

Instead of `<your-ip>` and `your-port` use your remote development IP and defined WordPress port. Afterwards you can `yarn docker:start` and use the boilerplate as usual.

## Open WordPress

Open a new terminal in the newly created workspace. Run [`yarn docker:start`](available-commands/root.md#development) and Docker will start the complete WordPress environment with your plugin activated on `localhost:{your-port}`. Open a web browser, navigate to `localhost:{your-port}/wp-admin` and enter the following credentials:

Username: `wordpress`  
Password: `wordpress`

## Initial commit

If you do an initial commit to your Git repository make sure to use `git commit [...] --no-verify` to skip hooks. This is needed because the boilerplate has some non-verifiable files in `common/create-wp-react-app`.
