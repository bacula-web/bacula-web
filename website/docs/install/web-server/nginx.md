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

    root /var/www/bacula-web/public;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    # optionally disable falling back to PHP script for the asset directories;
    # nginx will return a 404 error when files are not found instead of passing the
    # request to Symfony (improves performance but Symfony's 404 page is not displayed)
    # location /bundles {
    #     try_files $uri =404;
    # }

    location ~ ^/index\.php(/|$) {
        # when using PHP-FPM as a unix socket
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;

        # when PHP-FPM is configured to use TCP
        # fastcgi_pass 127.0.0.1:9000;

        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;

        # optionally set the value of the environment variables used in the application
        # fastcgi_param APP_ENV prod;
        # fastcgi_param APP_SECRET <app-secret-id>;
        # fastcgi_param DATABASE_URL "mysql://db_user:db_pass@host:3306/db_name";
        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP's OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        # Caveat: When PHP-FPM is hosted on a different machine from nginx
        #         $realpath_root may not resolve as you expect! In this case try using
        #         $document_root instead.
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        # Prevents URIs that include the front controller. This will 404:
        # http://example.com/index.php/some-path
        # Remove the internal directive to allow URIs like this
        internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/bacula-web_error.log;
    access_log /var/log/nginx/bacula-web_access.log;
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
