# Nginx

Follow instructions below to set up Bacula-Web with [Nginx](https://nginx.org/en/) instead of [Apache www](https://httpd.apache.org/).

:::info
Following instructions are for [Ubuntu 24.04 (Noble Numbat)](https://www.releases.ubuntu.com/noble/).

If you are using another debian based distribution, you may need to adapt some paths or packages name.
:::

## Install nginx & PHP-FPM

Download latest package lists

```shell
$ sudo apt-get update
```

Install Nginx and PHP-FPM

```shell
$ sudo apt-get install nginx php-fpm
```

## Configure PHP-FPM

Modify PHP-FPM configuration

```shell
$ sudo vim /etc/php/8.3/fpm/php.ini
    
cgi.fix_pathinfo=0
date.timezeone = Europe/Zurich
```

Restart PHP-FPM service

```shell
$ sudo systemctl php8.3-fpm restart
```

## Configure Nginx

Define a new virtual server configuration like below.

```
server {
  server_name bacula-web.domain.com;

  listen 80;
  listen [::]:80;

  root /var/www/bacula-web/public;

  index index.php;

  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php8.3-fpm.sock;
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
$ sudo systemctl restart nginx
```

You can now proceed with the installation [using Composer](../composer-install.md)
