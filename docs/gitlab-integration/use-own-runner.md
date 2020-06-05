# Use own runner

While using GitLab CI/CD functionality share runners are active by default. Shared runners does a great job except it comes to "more advanced" and powerful usage ([disadvantages](./predefined-pipeline.md#disadvantages)). GitLab has you covered! We do not know another CI/CD system giving the ability to run jobs so easily on your own server.

{% hint style="info" %}
Below is an example of how you can create exactly one own Gitlab CI runner for your repository, running Docker as an executor. The following steps may differ for more complex or scalable scenarios. For more advanced usage refer to the official [Runner documentation](https://docs.gitlab.com/runner).
{% endhint %}

### Setup an own runner

1. If you do not have yet your own server navigate to [Scaleway](https://scaleway.com) and order one (the cheapest Linux server should be enough)
1. SSH into your server (Scaleway explains while ordering how that works)
1. Install `docker` and `docker-compose` on that server (see [this](../usage/getting-started.md#prerequisites) for reference)
1. Install `gitlab-runner` (see [documentation](https://docs.gitlab.com/runner/install/linux-repository.html))
1. Register GitLab runner with Docker Executor onto your GitLab project or Group (see [documentation](https://docs.gitlab.com/runner/register/index.html))
1. Open the [configuration file](https://docs.gitlab.com/runner/configuration/advanced-configuration.html)and modify as follow (see below [example](#example-configuration) to know the exact position)
    - Add `concurrent = 4` so jobs inside stages run parallel
    - Add `"/var/run/docker.sock:/var/run/docker.sock"` in `volumes` so jobs can access docker daemon to run containers in containers
1. Restart Runner `gitlab-runner restart`

### Configure your GitLab project

1. Disable shared runners in GitLab > Project > CI/CD > Runners and enable the registered one
1. Add [variable](./extend-gitlab-ci-pipeline.md#available-variables) `$DOCKER_DAEMON_ALLOW_UP` with value `1` in GitLab > Project > CI/CD > Variables
1. Enable [Review applications](./review-applications.md)

{% hint style="info" %}
Note #1: You can use one registered `gitlab-runner` (executor) for multiple projects.
Note #2: If your server runs out of disk you need to configure a cleanup, read more [here](https://gitlab.com/gitlab-org/gitlab-runner-docker-cleanup).
{% endhint %}

## Use cache

By default, the `gitlab-runner` cache does not work as expected. You need to configure an own S3-compatible cache. Here, [minio](https://min.io) comes to the game.

1. Follow the complete introduction [here](https://docs.gitlab.com/runner/install/registry_and_cache_servers.html#install-your-own-cache-server)
1. Open the `gitlab-runner` configuration file and modify as follow (see below [example](#example-configuration) to know the exact position)
    - Copy the complete `[runners.cache]` section into your configuration
    - Adjust the values `XXX` to yours
    - Make sure `Insecure` is `false` because you do not have setup HTTPS with minio
1. Restart Runner `gitlab-runner restart`

Read more about it [here](https://docs.gitlab.com/runner/configuration/autoscale.html#distributed-runners-caching).

## Example configuration

A complete `gitlab-runner` with one executor configured can look like this:

```toml
concurrent = 4
check_interval = 0

[session_server]
  session_timeout = 1800

[[runners]]
  name = "XXX"
  url = "https://gitlab.com"
  token = "XXX"
  executor = "docker"
  [runners.docker]
    tls_verify = false
    image = "alpine:latest"
    privileged = false
    disable_entrypoint_overwrite = false
    oom_kill_disable = false
    disable_cache = false
    volumes = ["/var/run/docker.sock:/var/run/docker.sock", "/cache"]
    shm_size = 0
  [runners.cache]
    Type = "s3"
    Path = "XXX"
    Shared = false
    [runners.cache.s3]
      ServerAddress = "XXX"
      AccessKey = "XXX"
      SecretKey = "XXX"
      BucketName = "runner"
      Insecure = true
```
