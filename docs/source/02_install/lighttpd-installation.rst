.. _install/lighttpd-installation:

===================
Lighttpd web server
===================

Before you start
================

Before proceeding, ensure you had a look at the :ref:`install/requirements` page.

Install required packages
=========================

Follow instructions below to setup Bacula-Web using Lighttpd instead of Apache or Nginx web server

.. note:: The following instructions are based on Ubuntu 20.04 (Focal Fossa) using PHP 7.4.3

Install Lighttpd package

::

   $ sudo apt-get install -y lighttpd php-fpm

Install PHP extensions

::

   $ sudo apt install -y php-pdo php-xml php-pdo php-sqlite3 php-mysql php-pgsql php-cli php-xml

Configure Lighttpd
==================

Update Lighttpd server configuration

::

   $ cat /etc/lighttpd/lighttpd.conf
   ...
   # vhosts
   include "/etc/lighttpd/vhosts.d/*.conf"
   ...

Create the file below

::

   $ cat /etc/lighttpd/vhosts.d

   $HTTP["host"] == "ltd-bacula-web.domain.local" {
     server.document-root = "/var/www/html/bacula-web/public"
     server.errorlog = "/var/log/lighttpd/bacula-web-error.log"
   }

Running the config test should return this result

::

   $ sudo lighttpd -t -f /etc/lighttpd/lighttpd.conf
   Syntax OK

Restart and check Lighttpd server status

::

   $ sudo systemctl restart lighttpd

   $ sudo systemctl status lighttpd

If everything went well, you can now proceed with the installation using Composer, or the Composer package (follow one of the link below)

   * Install :ref:`using the archive<install/installarchive>`
   * Install :ref:`using Composer<install/installcomposer>`