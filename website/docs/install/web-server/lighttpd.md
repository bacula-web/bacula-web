# Lighttpd

## Install required packages

Follow instructions below to setup Bacula-Web using Lighttpd instead of Apache or Nginx web server

:::info
The following instructions are based on Ubuntu 20.04 (Focal Fossa) using PHP 8.0.28
:::

Install Lighttpd package

```shell
$ sudo apt-get install -y lighttpd php-fpm
```

Enable fastcgi configuration

```shell
$ sudo lighttpd-enable-mod fastcgi
$ sudo lighttpd-enable-mod fastcgi-php
```

then restart Lighttpd service

```shell
$ sudo systemctl restart lighttpd
```

Install PHP extensions

```shell
$ sudo apt install -y php-pdo php-xml php-pdo php-sqlite3 php-mysql php-pgsql php-cli php-xml
```

## Configure Lighttpd

Update Lighttpd server configuration

```shell
$ cat /etc/lighttpd/lighttpd.conf

# vhosts
include "/etc/lighttpd/vhosts.d/*.conf"
```

Create the folder below

```shell
$ cat /etc/lighttpd/vhosts.d
```

```
$HTTP["host"] == "ltd-bacula-web.domain.local" {
server.document-root = "/var/www/html/bacula-web/public"
server.errorlog = "/var/log/lighttpd/bacula-web-error.log"
}
```

Running the config test should return this result

```shell
$ sudo lighttpd -t -f /etc/lighttpd/lighttpd.conf
Syntax OK
```

Restart and check Lighttpd server status

```shell
$ sudo systemctl restart lighttpd

$ sudo systemctl status lighttpd
```

You can now proceed with the installation [using Composer](../composer-install.md)
