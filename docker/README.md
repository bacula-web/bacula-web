® Using Docker

## Building the Docker image

This section describe how to build Bacula-Web Docker image

Set version

```shell
# e.g: export ver=9.4.0
export ver=x.y.z
```

Clone from git repo
```shell
rm -rf src
git clone -b v$ver https://github.com/bacula-web/bacula-web.git src
```

Build using docker buildx

```shell
docker buildx build --load \
--no-cache \
--platform linux/amd64 \
--tag baculaweb/bacula-web:latest \
-f Dockerfile .
```

### PHP timezone

PHP timezone is set by default to UTC, to set another timezone run

*Example with timezone America/Los_Angeles*

```shell
docker buildx build --load \
--no-cache \
--platform linux/amd64 \
--tag baculaweb/bacula-web:latest \
--build-arg PHP_TZ="America/Los_Angeles" \
-f Dockerfile .
```

Clean-up temp source folder
```shell
rm -rf src
```

## Using the Docker image

See [Bacula-Web Docker image on DockerHub](https://hub.docker.com/r/baculaweb/bacula-web)

## Bug report and feature request

See [bug report and feature request](https://docs.bacula-web.org/en/latest/03_get-help/support.html) in documentation
