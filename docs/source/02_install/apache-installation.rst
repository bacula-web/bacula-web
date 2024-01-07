.. _install/apache-installation:

Apache
======

Before proceeding to Apache / PHP installation and configuration, read the :ref:`install/requirements` page.

Install requirements on RedHat / Centos / Fedora
------------------------------------------------

Install Apache web server on rpm based Linux distribution like Red Hat, Centos, Fedora, SUSE Linux, Scientific Linux, etc.

.. hint:: On latest Red Hat, Centos, Fedora, etc. systems, note the changes below
    - On Fedora, yum has been replaced by dnf
    - On Red Hat, Centos, Fedora, etc, service and chkfconfig command has been replaced by systemctl

::

$ sudo yum install httpd
$ sudo chkconfig httpd on
$ sudo service httpd start

Install PHP and PHP modules for the database you've installed for Bacula

MySql Bacula catalog

::

   $ sudo yum install php php-gettext php-mysql php-pdo

.. important:: On Fedora 36, install php-mysqlnd instead of php-mysql

postgreSQL Bacula catalog

::

   $ sudo yum install php php-gettext php-pgsql php-pdo

.. note:: RedHat / Centos 6 users might need to install php-posix or php-process packages

Install requirements on Gentoo
------------------------------

Modify portage configuration
    
::

   # File: /etc/portage/package.use
 
   # MySQL
   dev-lang/php mysql apache2 truetype cli pcre xml zlib
 
   # postgreSQL
   dev-lang/php postgres apache2 truetype cli pcre xml zlib

Install Apache and PHP

::

   $ sudo emerge -v php

.. 
   You can have a cup of coffee from now, it'll take a little bit of time ;)

Enable Apache to the default runlevel

::

   # rc-update add apache2 default

Then restart Apache

::

   # /etc/init.d/apache2 restart

Install requirements on Debian / Ubuntu / Linux Mint
----------------------------------------------------

Install Apache and PHP

::

   $ sudo apt-get install apache2 libapache2-mod-php php-sqlite

   With MySQL support
   
   $ sudo apt-get install php7.0-mysql

   With postgreSQL support

   $ sudo apt-get install php7.0-pgsql

.. note:: On older Debian or Ubuntu versions, you need to use PHP 5

   $ sudo apt-get install apache2 libapache2-mod-php5 php5-sqlite 

Install requirements on FreeBSD
-------------------------------

You can start with a fresh FreeBSD 9.0 installation, with ports from original CD media, not updated to keep as simple as possible.

Modify /etc/make.conf (might not exist yet)

::

   # vim /etc/make.conf
   WITHOUT_X11=yes

*This is done to keep the graphics/php-gd port from installing extra stuff for X, not having it will not stop anything from working.*

Install required ports

Here's below a list of FreeBSD ports you need to install

   * databases/postgresql91-server
   * sysutils/bacula-client
   * www/apache22
   * lang/php5
   * www/php5-session
   * devel/php5-gettext

With PostgreSQL bacula catalog
   * databases/php5-pdo_pgsql
   * databases/php5-pgsql

With MySQL bacula catalog
   * databases/php5-mysql
   * databases/php5-pdo_mysql

.. note:: A big thanks to Dean E. Weimer who provided me Bacula-Web installation instructions for \*BSD setup

Apache web server configuration
-------------------------------

PHP configuration
-----------------

Update the timezone parameter in your PHP configuration in order to prevent Apache warning messages (see below)

::

   Warning: mktime(): It is not safe to rely on the system's timezone settings. You are *required* to use the date.timezone setting or the date_default_timezone_set() function. In case you used any of those methods and you are still getting this warning, you most likely misspelled the timezone identifier. We selected 'Europe/Berlin' for 'CEST/2.0/DST' instead in /var/www/html/bacula-web/config/global.inc.php on line 62

Modify php.ini configuration file

::

   File: /etc/php.ini
   # For *BSD users, the file is located /usr/local/etc/php.ini
    
   # Locate and modify the line below
   date.timezone = 
    
   # with this value (for example)
   date.timezone = Europe/Zurich

Reload Apache configuration

::

   $ sudo service httpd reload || sudo /etc/init.d/httpd reload

Apache virtualhost
------------------

In order to secure the application folder and avoid exposing sensitive information contained in Bacula-Web configuration.

Edit the Apache configuration file as described below

**Red Hat / Centos / Fedora**

::

   $ sudo vim /etc/httpd/conf.d/bacula-web.conf

**Debian / Ubuntu**

::

   $ sudo vim /etc/apache2/sites-available/bacula-web.conf

with the content below

::

   <VirtualHost *:80>
     DocumentRoot "/var/www/html/bacula-web/public"
     ServerName bacula-web.domain.com
         
     <Directory /var/www/html/bacula-web/public>
       AllowOverride All
     </Directory>

     # More directives here ...
   </VirtualHost>

You might need to adapt Bacula-Web installation path in the above configuration according to your setup

.. important:: As of version 8.6.0, the DocumentRoot must be set to the public sub-folder.

Enable the configuration

::

    $ sudo a2ensite bacula-web

Then restart Apache to apply the configuration change

**Red Hat / Centos / Fedora**

::

   $ sudo /etc/init.d/httpd restart

**Debian / Ubuntu**

::

   $ sudo /etc/init.d/apache2 restart

If everything went well, you can now proceed with the installation using Composer, or the Composer package (follow one of the link below)

   * Install :ref:`using Composer<install/installcomposer>`