# Install using Composer

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';

Since version 8.0.0, Bacula-Web dependencies are managed using [Composer](https://getcomposer.org/).

As stated on [Composer's official website](https://getcomposer.org/doc/00-intro.md#dependency-management).

*Composer is a tool for dependency management in PHP.
It allows you to declare the libraries your project depends on, and it will manage (install/update) them for you.*

Let's start by installing Composer on your system

## Install Composer

Most Linux distro provides Composer as package, so to install it run this command

<Tabs>
  <TabItem value="rpm" label="For Red Hat / Centos / Fedora" default>
```shell
$ sudo yum install composer
```
  </TabItem>
  <TabItem value="deb" label="For Debian / Ubuntu">
```shell
$ sudo apt-get install composer
```
  </TabItem>
</Tabs>

If your distro doesn't provide Composer package, Composer website contains all information
you can install it manually as explained on [this page](https://getcomposer.org/download/).

:::warning
Make sure you're using latest Composer version. Please have a look at [Packagist.com website](https://getcomposer.org/2).
:::

## Install with Composer

From your $HOME folder, run the command below

```shell
$ composer create-project --no-dev --prefer-dist bacula-web/bacula-web bacula-web
```

Once done, you can check Bacula-Web installation by running the command below

```shell
$ cd bacula-web && composer check

Checking platform requirements for packages in the vendor dir
ext-dom       20031129   success
ext-gettext   8.0.28     success
ext-json      8.0.28     success
ext-libxml    8.0.28     success
ext-mbstring  *          success provided by symfony/polyfill-mbstring
ext-openssl   8.0.28     success
ext-pcre      8.0.28     success
ext-pdo       8.0.28     success
ext-phar      8.0.28     success
ext-posix     8.0.28     success
ext-simplexml 8.0.28     success
ext-sqlite3   8.0.28     success
ext-tokenizer 8.0.28     success
ext-xml       8.0.28     success
ext-xmlwriter 8.0.28     success
php           8.0.28     success
```

## Fix files/folders ownership and permissions

Move the folder in the web server folder

```shell
$ sudo mv -v bacula-web /var/www/
```

Follow instructions to to [set permissions and ownership](../guides/permissions-and-ownership.mdx) in your Bacula-Web folder.

Once you're done, please proceed to the [configuration](configure.md) of Bacula-Web.
 