# Nginx

Follow instructions below to set up Bacula-Web with [Nginx](https://nginx.org/en/) instead of [Apache www](https://httpd.apache.org/).

:::info
Following instructions are for [Ubuntu 24.04 (Noble Numbat)](https://www.releases.ubuntu.com/noble/).

If you are using another Debian distribution, you may need to adapt some paths and/or the name of package(s).
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
    listen 80;
    server_name bacula-web.domain.com;
    index index.php;
    error_log /var/log/nginx/bacula-web.error.log;
    access_log /var/log/nginx/bacula-web.access.log;
    root /var/www/bacula-web/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        fastcgi_pass 127.0.0.1:9123;
    }
}
```

:::warning
Please note that as of version 8.6.0, the DocumentRoot must be set to the `public` sub-directory.
:::

Test your configuration

```shell
sudo nginx -t && echo "Nginx is ok"
```

Restart Nginx to apply configuration changes

```shell
sudo systemctl restart nginx
```

You can now proceed with any of the installation method below

- [Using the archive](../archive-install.md)
- [Using Composer](../composer-install.md)
