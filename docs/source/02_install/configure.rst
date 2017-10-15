.. _install/configure:

####################
Configure Bacula-Web
####################

From Bacula-Web root folder, copy the file config.php.sample as below

.. note:: Please note that since version 5.1.0, the config file is a PHP script.*

*************************
Create configuration file
*************************

This section explain how to create the configuration which contain your custom settings.

All Bacula-Web settings are stored in the file below

::

    application/config/config.php

From Bacula-Web root folder, copy the sample config file and give it the name *config.php*
    
::

   # cd application/config
   # cp -v config.php.sample config.php

Now you need to make sure config.php has the correct permissions.
The configuration file needs to be at least readable by the user owning the web server process.

**On Red Hat / Centos / Fedora**

Apache user is **apache**

**On Debian / Ubuntu**

Apache user is **www-data**

So depending on which system you have installed Bacula-Web, run the command below

::

   # chown -v <apache_user>: config.php

For example, on Debian Jessie, use this command

::

   # chown -v www-data: config.php 

on Centos 7

::

   # chown -v apache: config.php

.. note:: Do not forget the column <:> after apache_user 

*********
Settings
*********

General settings
================

================================= ====================================== ================
Setting                           Description                            Default value
================================= ====================================== ================
$config['language']               Set displayed language                 en_US
$config['show_inactive_clients']  Show inactive clients or not           true
$config['datetime_format']        Change default date and time format    Y-m-d H:i:s 
$config['hide_empty_pools']       Hide empty pools                       true
================================= ====================================== ================

language
--------

**Description**

As Bacula-Web is translated into not less than 15 languages, the language setting allow you to change the displayed 
language of Bacula-Web.

You can simply change from english to your language by modifying the $config['language'] value.

**Example** 

::

   $config['language'] = 'pt_BR'; // For portuguese brazilian 
                             
hide_empty_pools 
----------------

**Description**

Do not display empty pools in Dashboard (Pools and volumes widget)

**Example**

::

   $config['hide_empty_pools'] = false;
                             
.. note:: this setting is availabe since Bacula-Web 5.2.11

show_inactive_clients
---------------------

**Description**

If disabled (set to *false*), don't list or show inactive clients

**EXample**

::

   $config['show_inactive_clients'] = true;

.. note:: this setting is availabe since Bacula-Web 5.2.11

datetime_format
---------------

**Description**

Define your customer date & time format (by default Y-m-d H:i:s)

For more information on date format, have a look on date() function in `PHP manual`_



**Example**

::

   $config['datetime_format'] = 'd/m/Y H:i:s';
   or
   $config['datetime_format'] = 'm-d-Y H:i:s';

.. note:: this setting is availabe only since version 7.4.0 

Database connection settings

::

   // Bacula catalog label (used for catalog selector)
   $config[0]['label'] = 'Backup Server';
                             
   // Server
   $config[0]['host'] = 'localhost';
                             
   // Database name
   $config[0]['db_name'] = 'bacula';
                             
   // Database user
   $config[0]['login'] = 'bacula';

   // Database user's password
   $config[0]['password'] = 'verystrongpassword';
                             
   // Database type (mysql | pgsql | sqlite)
   $config[0]['db_type'] = 'mysql';
                             
   // Database port
   $config[0]['db_port'] = '3306';

Single catalog (example)

::

   $config['language'] = 'en_EN';

   //MySQL bacula catalog
   $config[0]['label'] = 'Backup Server';
   $config[0]['host'] = 'localhost';
   $config[0]['login'] = 'bacula';
   $config[0]['password'] = 'verystrongpassword';
   $config[0]['db_name'] = 'bacula';
   $config[0]['db_type'] = 'mysql';
   $config[0]['db_port'] = '3306';

Multiple catalogs (example)

::

   <?php
   //MySQL bacula catalog
   $config[0]['label'] = 'Backup Server';
   $config[0]['host'] = 'localhost';
   $config[0]['login'] = 'bacula';
   $config[0]['password'] = 'verystrongpassword';
   $config[0]['db_name'] = 'bacula';
   $config[0]['db_type'] = 'mysql';
   $config[0]['db_port'] = '3306';

   //PostgreSQL Lab serveur
   $config[1]['label'] = 'Lab backup server';
   $config[1]['host'] = '192.168.0.120';
   $config[1]['login'] = 'bacula';
   $config[1]['password'] = 'verystrongpassword';
   $config[1]['db_name'] = 'bacula';
   $config[1]['db_type'] = 'pgsql';
   $config[1]['db_port'] = '5432';
   ?>

Configuration example

Here's below how your configuration file (config.php) could look like

::

   <?php
   // Language
   $config[0]['language'] = 'en_EN';

   // Show inactive clients
   $config['show_inactive_clients'] = false;

   // Hide empty pools
   $config['hide_empty_pools'] = true;

   //MySQL bacula catalog
   $config[0]['label'] = 'Backup Server';
   $config[0]['host'] = 'localhost';
   $config[0]['login'] = 'baculaweb';
   $config[0]['password'] = 'password';
   $config[0]['db_name'] = 'bacula';
   $config[0]['db_type'] = 'mysql';
   $config[0]['db_port'] = '3306';

   // PostgreSQL bacula catalog
   $config[1]['label'] = 'Prod Server';
   $config[1]['host'] = 'db-server.domain.com';
   $config[1]['login'] = 'bacula';
   $config[1]['password'] = 'otherstrongpassword';
   $config[1]['db_name'] = 'bacula';
   $config[1]['db_type'] = 'pgsql';
   $config[1]['db_port'] = '5432';

   // SQLite bacula catalog
   $config[2]['db_type'] = 'sqlite';
   $config[2]['label'] = 'bacula';
   $config[2]['db_name'] = '/path/to/database';
   ?>

.. _PHP manual: http://php.net/manual/en/function.date.php
