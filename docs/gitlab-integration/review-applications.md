# Review applications

GitLab explains it as best in this [article](https://docs.gitlab.com/ee/ci/review_apps/):

> -   Provide an automatic live preview of changes made in a feature branch by spinning up a dynamic environment for your merge requests.
> -   Allow designers and product managers to see your changes without needing to check out your branch and run your changes in a sandbox environment.
> -   Allow you to deploy your changes wherever you want.

The boilerplate comes with [predefined jobs](./predefined-pipeline.md#release) that allows creating a review application in a feature branch (non-`master` branches). But for this another tool [Traefik](https://traefik.io/) needs to be installed on [your runner server](./use-own-runner.md). Here is a very handy tutorial doing that:

{% hint style="info" %}
When talking in the next steps about to replace the server IP then put your IP of your GitLab CI Runner server and replace `.` with `-`, for example `192-168-1-250`.
{% endhint %}

1. SSH into your GitLab CI Runner
1. Install `apache2-utils` for Basic-Auth generation: `sudo apt install apache2-utils`
1. Create a password for the Traefik dashboard, replace `secure_password` with your password: `htpasswd -nb admin secure_password`
1. `cd /opt/ && sudo nano traefik.toml`: Create a Traefik [configuration file](https://docs.traefik.io/getting-started/configuration-overview/). Copy the content of [Example configuration](#example-configuration) below and replace `your-generated-htpasswd` with the output of the previous command
1. Create an unique network for Traefik which should be accessable by the web: `sudo docker network create traefik`
1. Create the Traefik container handling all the routing: `sudo docker run -d -v /var/run/docker.sock:/var/run/docker.sock -v $PWD/traefik.toml:/traefik.toml -p 80:80 -p 443:443 -l traefik.enable=true -l traefik.frontend.rule=Host:monitor-<your-server-ip>.nip.io -l traefik.port=8080 --network traefik --name traefik traefik:1.7.12-alpine`
1. Navigate to `monitor-<your-server-ip>.nip.io`, enter the credentials your generated with `htpasswd` and user `admin` and you will see the Traefik dashboard
1. Navigate to GitLab > Project > Settings > CI / CD > Variables and add the [variable](./extend-gitlab-ci-pipeline.md#available-variables) `$CI_TRAEFIK_HOST` with value `<your-server-ip>.nip.io`
1. **Securing review apps itself?** Yes, that's possible with Basic Authentication within Traefik and also necessary for this boilerplate
1. Additionally generate a new review user with `htpasswd -nb admin secure_password` and store that output as value for the GitLab CI Runner [variable](./extend-gitlab-ci-pipeline.md#available-variables) `$CI_TRAEFIK_BAUTH`. **Note**: `$` must be doubled `$$` for escaping!
1. Navigate to GitLab > Project > Settings > CI / CD > Runners and edit your used runner, you must add the `traefik` tag so jobs are taken correctly only on this runner

{% hint style="success" %}
**Awesome!** If you commit to a non-`master` branch a dynamic environment is created in GitLab > Project > Operations > Environments.
{% endhint %}

{% hint style="warning" %}
**SSL certificates**: Due to some experience in productive usage of the boilerplate it is currently not possible to use `nip.io` together with Traefik and "Let's Encrypt". So we had adjusted the boilerplate to use HTTP requests instead of HTTPS.
{% endhint %}

## Example configuration

A complete `traefik` configuration can look like this:

```toml
defaultEntryPoints = ["http"]

[entryPoints]
  [entryPoints.dashboard]
    address = ":8080"
    [entryPoints.dashboard.auth]
      [entryPoints.dashboard.auth.basic]
        # Secure admin dashboard with an "admin" password
        users = ["your-generated-htpasswd"]
  [entryPoints.http]
    address = ":80"
  [entryPoints.https]
    address = ":443"
    [entryPoints.https.redirect]
      entryPoint = "http"

[api]
entrypoint="dashboard"

[docker]
domain = "your-server-ip.nip.io"
watch = true
network = "traefik"
exposedByDefault = false
```
