.. _install/installnginx:

================
Nginx web server
================

Before you start
================

Before proceeding to Nginx / fpm / PHP installation and configuration, read the :ref:`install/requirements` page.

Install required packages
=========================

Follow instructions below to setup Bacula-Web with Nginx instead of Apache www

.. note:: These instruction have been tested with Ubuntu 16.04 (Xenial).

Download latest package lists

::

    $ sudo apt-get update

Install Nginx and PHP-FPM

::

    $ sudo apt-get install nginx php-fpm php-sqlite3 php-gd

If you use MySQL Bacula catalog

::

    $ sudo apt-get install php-mysql

If you use postgreSQL Bacula catalog

::

    $ sudo apt-get install php-pgsql


Configure PHP-FPM
=================

Modify PHP-FPM configuration

::

    $ sudo vim /etc/php/7.3/fpm/php.ini
    
    cgi.fix_pathinfo=0
    date.timezeone = Europe/Zurich

Restart PHP-FPM service

::

    /etc/init.d/php7.3-fpm restart


Configure Nginx
===============

Define a new virtual server configuration like below.

::

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

.. important:: Please note that as of version 8.6.0, the DocumentRoot must be set to the public sub-folder.

Test your configuration

::

    $ sudo nginx -t && echo "Nginx is ok"

Restart Nginx to apply configuration changes

::

    $ sudo /etc/init.d/nginx restart

Once your web server is ready, you can proceed with Bacula-Web installation.

You have now two different options

   * Install :ref:`using the archive<install/installarchive>`
   * Install :ref:`using Composer<install/installcomposer>`
