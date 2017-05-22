.. _install/install:

===================
Install and upgrade
===================

Install required packages on RedHat / Centos / Fedora
-----------------------------------------------------

Install Apache web server

::

$ sudo yum install httpd
$ sudo chkconfig httpd on
$ sudo service httpd start

Install PHP and PHP modules for the database you've installed for Bacula

MySql Bacula catalog

::

   $ sudo yum install php php-gd php-gettext php-mysql php-pdo

postgreSQL Bacula catalog

::

   $ sudo yum install php php-gd php-gettext php-pgsql php-pdo

SQLite database support

::

   $ sudo yum install php php-gd php-gettext php-pdo

Change SQLite database file permissions

Assuming that the bacula database file is located under /var/spool/bacula

::

   # chmod -v 755 /var/spool/bacula
   # chmod -v 704 /var/spool/bacula/bacula.db

Install on Gentoo
-----------------

Modify portage configuration
    
::

   # File: /etc/portage/package.use
 
   # MySQL
   dev-lang/php mysql gd apache2 truetype cli pcre xml zlib
 
   # postgreSQL
   dev-lang/php postgres gd apache2 truetype cli pcre xml zlib
 
   # SQLite
   dev-lang/php sqlite gd apache2 truetype cli pcre xml zlib

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

Install on Debian / Ubuntu / Linux Mint
_______________________________________

Install Apache and PHP

::

   With MySQL support
   
   $ sudo apt-get install apache2 libapache2-mod-php5 php5-mysql php5-gd

   With postgreSQL support

   $ sudo apt-get install apache2 libapache2-mod-php5 php5-pgsql php5-gd

   With SQLite support

   $ sudo apt-get install apache2 libapache2-mod-php5 php5-sqlite php5-gd

Install on FreeBSD
------------------

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
   * graphics/php5-gd

With PostgreSQL bacula catalog
   * databases/php5-pdo_pgsql
   * databases/php5-pgsql

With MySQL bacula catalog
   * databases/php5-mysql
   * databases/php5-pdo_mysql

With SQLite bacula catalog
   * databases/php5-sqlite
   * databases/php5-pdo_sqlite

**Special note**

A big thanks to Dean E. Weimer who provided me theses useful details for *BSD setup
