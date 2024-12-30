# PHP

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';

## PHP configuration

### timezone

Update the timezone parameter in your PHP configuration in order to prevent Apache warning messages (see below)

`Warning: mktime(): It is not safe to rely on the system's timezone settings. You are *required* to use the date.timezone setting or the date_default_timezone_set() function. In case you used any of those methods and you are still getting this warning, you most likely misspelled the timezone identifier. We selected 'Europe/Berlin' for 'CEST/2.0/DST' instead in /var/www/html/bacula-web/config/global.inc.php on line 62`

Modify `php.ini` configuration file

File: `/etc/php.ini`

:::tip
For *BSD users, the file is located `/usr/local/etc/php.ini`
:::

Locate and modify the line below

`; date.timezone =`

with this value (for example)

`date.timezone = Europe/Zurich`

## Install PHP extensions

### For rpm based OS

For Centos, RHEL, Fedora, RockyLinux, OracleLinux users, install PHP and PHP extensions for the database you've installed for Bacula

<Tabs>
  <TabItem value="mysql" label="MySQL Bacula catalog" default>
```shell
$ sudo yum install php php-gettext php-pdo  php-mysql
```
  </TabItem>
  <TabItem value="postgres" label="postgreSQL Bacula catalog">
```shell
$ sudo yum install php php-gettext php-pdo php-pgsql
```
  </TabItem>
  <TabItem value="sqlite" label="SQLite Bacula catalog">
```shell
$ sudo yum install php php-gettext php-pdo php-sqlite3
```
 </TabItem>
</Tabs>

:::tip
On Fedora 36, install `php-mysqlnd` instead of `php-mysql`

RedHat / Centos 6 users might need to install `php-posix` or `php-process` packages
:::

### For deb based OS

For Debian, Ubuntu, Linux Mint users

Install PHP and PHP extensions

<Tabs>
  <TabItem value="mysql" label="MySQL Bacula catalog" default>
```shell
$ sudo apt-get install php php-gettext php-mysql
```
  </TabItem>
  <TabItem value="postgres" label="postgreSQL Bacula catalog">
```shell
$ sudo apt-get install php php-gettext php-pgsql
```
  </TabItem>
  <TabItem value="sqlite" label="SQLite Bacula catalog">
```shell
$ sudo apt-get install php php-gettext php-sqlite3
```
 </TabItem>
</Tabs>

### For Gentoo users

Modify portage configuration

File: `/etc/portage/package.use`

```
# MySQL
dev-lang/php mysql apache2 truetype cli pcre xml zlib

# postgreSQL
dev-lang/php postgres apache2 truetype cli pcre xml zlib

# SQLite
dev-lang/php sqlite apache2 truetype cli pcre xml zlib
```

Install PHP and PHP extensions

```shell
$ sudo emerge -v php
```

You can have a cup of coffee from now, it'll take a little bit of time ;)

:::note
Above instructions might be outdated.
:::

## Note for SQLite Bacula catalog

Change SQLite database file permissions

Assuming that the bacula database file is located under /var/spool/bacula

```shell
$ sudo chmod -v 755 /var/spool/bacula
$ sudo chmod -v 704 /var/spool/bacula/bacula.db
```
