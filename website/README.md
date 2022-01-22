# bacula-web.org website

## About this git repository

Bacula-Web website content is generated using [Hugo](https://gohugo.io/).

This Repository Git contains the data available via the URL <https://www.baculata-web.org>

## Contributing

You can contribute to this git repository by following the steps below

- fork this [project](https://github.com/bacula-web/www.bacula-web.org) on Github
- commit your changes / fixes
- create a pull request

## License

Content of this website is licensed under [GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)

## Running the website locally using Docker

Clone the git repository

```shell
git clone https://github.com/bacula-web/www.bacula-web.org.git
```

and run this command to run a Docker container

```shell
docker run --rm -i -t -v $(pwd):/src --name bacula-web-website -p 1313:1313 klakegg/hugo:<tag> shell
```

**Important note**
> adapt <tag> with latest Docker image tag, but make sure you're using the extended version of Hugo
> example: klakegg/hugo:0.83.1-ext-alpine

Open a terminal into the Docker container

```shell
cd www.bacula-web.org
docker exec -i -t bacula-web-website bash
```

then start  Hugo builtin web server
```shell
hugo server

# To show content in draft, run this command
hugo server -D
```
