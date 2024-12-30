# Installation

## Introduction

Before starting the installation of Bacula-Web, please you meet the requirements listed below

* you have access to the server using ssh or console access
* you have root access or sudo privileges

:::tip
Installing Bacula-Web using root account is not recommended, use a regular account with sudo privileges
:::

## Steps

### Check requirements

Make sure your system meets the minimal requirements by checking the [requirement](requirements.md) page

### Installation alternatives

Bacula-Web can be installed in different way, but the recommended way is by using Composer

You will find more details by following the links below

* Install [using Composer](composer-install.md)
* Install [using Docker](docker-install.md)
* Install [from source](source-install.md)

:::info
Installation using the archive which used to be available on GitHub releases is not supported anymore due to several issues, such as conflicts
between different PHP versions used by community users.
:::

### Web server alternatives

You can pick one of the web server listed below to install Bacula-Web on your server

* [Apache web server](web-server/apache.md)
* [Nginx web server](web-server/nginx.md)
* [Lighttpd web server](web-server/lighttpd.md)

### Configure

Please follow instructions from the [configure](configure.md) page.

### Set users authentication

Unless you've disabled user authentication (see [enable_users_auth](configure.md#enable_users_auth)) settings,  proceed to [users authentication setup](setup-user-auth.md).

:::warning
If you have not disabled user authentication, **YOU MUST** setup user authentication by following instructions from [this page](setup-user-auth).
:::

To make sure your setup is in good shape, follow instructions from the [Testing](test.md) page.
