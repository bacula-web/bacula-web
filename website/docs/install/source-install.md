# Install from source

Installation from source is mainly used for development purpose, or to [contribute on the project source code](../contribute/source-code.md)

## Pre-requisite

* [`git`](https://git-scm.com/)
* [`composer`](https://getcomposer.org/) (at least version 2)

## Prepare the source

Clone Bacula-Web git repository

```shell
$ git clone https://github.com/bacula-web/bacula-web.git
$ cd bacula-web
```

Checkout latest release tag (see [latest release tag](https://github.com/bacula-web/bacula-web/tags))

```shell
$ git checkout v9.7.0
```

:::tip
Above example use [9.7.0 release tag](https://github.com/bacula-web/bacula-web/releases/tag/v9.7.0)
:::

Install dependencies using Composer

```shell
$ composer install --no-dev
```

## Fix files/folders ownership and permissions

Move the folder in the web server folder

```shell
$ sudo mv -v bacula-web /var/www/
```

Follow instructions to [set permissions and ownership](../admin-guides/permissions-and-ownership.mdx) in your Bacula-Web folder.

## Next step

You can now proceed to Bacula-Web [configuration](configure.md).