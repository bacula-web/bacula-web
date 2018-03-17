.. _install/installnginx:

#############################
Nginx web server installation
#############################

*************************
Install required packages
*************************

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


*****************
Configure PHP-FPM
*****************

Modify PHP-FPM configuration

::

    $ sudo vim /etc/php/7.1/fpm/php.ini
    
    cgi.fix_pathinfo=0
    date.timezeone = Europe/Zurich

Restart PHP-FPM service

::

    /etc/init.d/php7.1-fpm restart


***************
Configure Nginx
***************

Modify Nginx default site configuration

::

    $ sudo vim /etc/nginx/sites-enables/default
    
    # Add index.php to the list if you are using PHP
    index index.php index.html index.htm index.nginx-debian.html;

    fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;

Restart Nginx to apply conifguration changes

::

    $ sudo /etc/init.d/nginx restart

