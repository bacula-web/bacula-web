# Use Bacula-Web with Docker

## Building the Docker image

This section describe how to build Bacula-Web Docker image on your server or local machine

Clone the [bacula-web git repository](https://github.com/bacula-web/bacula-web)

```shell
git clone https://github.com/bacula-web/bacula-web.git
```

Checkout the latest git tag (latest tag can be found at https://github.com/bacula-web/bacula-web/tags)

```shell
export tag=X.Y-Z
git checkout v$tag
```

Build the image

```shell
docker buildx build \
  --load \
  --no-cache \
  --platform linux/amd64 \
  --tag bacula-web \
  -f docker/Dockerfile .
```

You can modify the platform If the target platform is different than `linux/amd64` 

## Environment variable

### PHP timezone

The default timezone is set by default to UTC, to set another timezone use the command below

*Example with timezone America/Los_Angeles*

```shell
docker buildx build --load \
--no-cache \
--platform linux/amd64 \
--tag baculaweb/bacula-web:latest \
--build-arg PHP_TZ="America/Los_Angeles" \
-f docker/Dockerfile .
```

## Deployment

Further instructions are provided on [DockerHub](https://hub.docker.com/r/baculaweb/bacula-web)

## Bug report and feature request

See [bug report and feature request](https://www.bacula-web.org/docs/gethelp/support) in documentation
