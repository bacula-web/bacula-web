# Nginx

Follow instructions below to setup Bacula-Web with Nginx instead of Apache www

:::info
These instruction have been tested with Ubuntu 22.04 (Jammy Jellyfish).
:::

Download latest package lists

```shell
$ sudo apt-get update
```

Install Nginx and PHP-FPM

```shell
$ sudo apt-get install nginx php-fpm
```

If you use MySQL Bacula catalog

```shell
$ sudo apt-get install php-mysql
```

If you use postgreSQL Bacula catalog

```shell
$ sudo apt-get install php-pgsql
```

## Configure PHP-FPM

Modify PHP-FPM configuration

```shell
$ sudo vim /etc/php/8.1/fpm/php.ini
    
cgi.fix_pathinfo=0
date.timezeone = Europe/Zurich
```

Restart PHP-FPM service

```shell
/etc/init.d/php8.1-fpm restart
```

## Configure Nginx

Define a new virtual server configuration like below.

```
    server {
      server_name bacula-web.domain.com;

      listen 80;
      listen [::]:80;

      root /var/www/html/bacula-web/public;

      index index.php;

      location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
      }

      location / {
        try_files $uri $uri/ /index.php?$query_string;
      }
    }
```

:::warning
Please note that as of version 8.6.0, the DocumentRoot must be set to the `public` sub-folder.
:::

Test your configuration

```shell
$ sudo nginx -t && echo "Nginx is ok"
```

Restart Nginx to apply configuration changes

```shell
$ sudo /etc/init.d/nginx restart
```

You can now proceed with the installation [using Composer](../composer-install.md)
