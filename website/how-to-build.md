# How to build this website

## Running the website locally using Docker

Clone the git repository

```shell
git clone https://github.com/bacula-web/www.bacula-web.org.git
```

and run this command to open a shell in a Docker container

```shell
docker run --rm -i -t -v $(pwd):/src --name bacula-web-website -p 1313:1313 klakegg/hugo:<tag> shell
```

## Build

```shell
docker run --rm -it -e HUGO_ENV=production -v $(pwd):/src klakegg/hugo:0.105.0-ext-alpine -b https://www.bacula-web.org
```

Open a terminal into the Docker container

```shell
cd www.bacula-web.org
docker exec -i -t bacula-web-website bash
```

then start Hugo builtin web server

```shell
hugo server

# To show content in draft, run this command
hugo server -D
```

You can also run this command

```shell
docker run --rm -it -v $(pwd):/src --name website -p 1313:1313 klakegg/hugo:0.105.0-ext-alpine server -D
```

### Quick note related to git sub-module

Hugo theme is setup as a git submodule. 

Once you have cloned the git repo from GitHub, run this command to update the git submodule

`git submodule update --init --recursive`

For more details, see [Git tools submodules](https://git-scm.com/book/en/v2/Git-Tools-Submodules)

### Update Hugo theme to latest version

Find more details by reading [Update your Docsy submodule](https://www.docsy.dev/docs/updating/updating-submodules/#update-your-docsy-submodule) on docsy.dev documentation website
