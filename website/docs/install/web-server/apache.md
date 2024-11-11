# Apache

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';

## Per Linux distributions

### On RedHat / Centos / Fedora

Install Apache web server on rpm based Linux distribution like Red Hat, Centos, Fedora, SUSE Linux, Scientific Linux, etc.

```shell
$ sudo yum install httpd
$ sudo chkconfig httpd on
$ sudo service httpd start
```

:::tip
On latest Red Hat, Centos, Fedora, etc. systems, note the changes below
* On Fedora, yum has been replaced by `dnf`
* On Red Hat, Centos, Fedora, etc, `service` and `chkfconfig` command has been replaced by `systemctl`
:::

### On Debian / Ubuntu / Linux Mint

Install Apache

```shell
$ sudo apt-get install apache2 libapache2-mod-php
```

### On Gentoo

Enable Apache to the default runlevel

```shell
$ sudo rc-update add apache2 default
```

Then restart Apache

```shell
$ sudo /etc/init.d/apache2 restart
```

### On FreeBSD

:::info
A big thanks to Dean E. Weimer who provided me Bacula-Web installation instructions for \*BSD setup
:::

You can start with a fresh FreeBSD 9.0 installation, with ports from original CD media, not updated to keep as simple as possible.

Modify `/etc/make.conf` (might not exist yet)

```shell
WITHOUT_X11=yes
```

This is done to keep the `graphics/php-gd` port from installing extra stuff for X, not having it will not stop anything from working.

Install required ports

Here's below a list of FreeBSD ports you need to install

* `databases/postgresql91-server`
* `sysutils/bacula-client`
* `www/apache22`
* `lang/php5`
* `www/php5-session`
* `devel/php5-gettext`

With PostgreSQL bacula catalog

* `databases/php5-pdo_pgsql`
* `databases/php5-pgsql`

With MySQL bacula catalog

* `databases/php5-mysql`
* `databases/php5-pdo_mysql`

With SQLite bacula catalog

* `databases/php5-sqlite`
* `databases/php5-pdo_sqlite`

:::warning
Instructions above are outdated, for more accurate instructions, please look at the [Bacula-Web port on FreshPorts.org](https://www.freshports.org/www/bacula-web/)
:::

## Apache configuration

### Apache virtualhost

In order to secure the application folder and avoid exposing sensitive information contained in Bacula-Web configuration.

Edit the Apache configuration file as described below

<Tabs>
  <TabItem value="rpm" label="For Red Hat / Centos / Fedora" default>
```shell
$ sudo vim /etc/httpd/conf.d/bacula-web.conf
```
  </TabItem>
  <TabItem value="deb" label="For Debian / Ubuntu">
```shell
$ sudo vim /etc/apache2/sites-available/bacula-web.conf
```
  </TabItem>
</Tabs>

with the content below

```
<VirtualHost *:80>
DocumentRoot "/var/www/html/bacula-web/public"
ServerName bacula-web.domain.com

     <Directory /var/www/html/bacula-web/public>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
     </Directory>

     # More directives here ...
   </VirtualHost>
```

You might need to adapt Bacula-Web installation path in the above configuration according to your setup

:::warning
As of version 8.6.0, the DocumentRoot must be set to the `public` sub-folder.
:::

Enable the configuration

```shell
$ sudo a2ensite bacula-web
```

Then restart Apache to apply the configuration change

<Tabs>
  <TabItem value="rpm" label="For Red Hat / Centos / Fedora" default>
```shell
$ sudo /etc/init.d/httpd restart
```
  </TabItem>
  <TabItem value="deb" label="For Debian / Ubuntu">
```shell
$ sudo /etc/init.d/apache2 restart
```
  </TabItem>
</Tabs>

You can now proceed with the installation [using Composer](../composer-install.md)
