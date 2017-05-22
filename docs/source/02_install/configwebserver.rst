.. _install/configwebserver

========================
Configure the web server
========================

Configure PHP
-------------

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

Secure your web server
----------------------

In order to secure the application folder and avoid exposing sensitive information contained in Bacula-Web configuration.

Edit the Apache configuration file as described below
Red Hat / Centos / Fedora


::

   $ sudo vim /etc/httpd/conf.d/bacula-web.conf

Debian / Ubuntu

::

   $ sudo vi /etc/apache2/sites-available/default

and add the content below

::

   <Directory /var/www/html/bacula-web>
   AllowOverride All
   </Directory>
   
Then reload Apache to apply the configuration change

Centos / Red Hat

::

   $ sudo /etc/init.d/httpd restart

Debian / Ubuntu

::

   $ sudo /etc/init.d/apache2 restart
