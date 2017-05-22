.. _install/configure:

====================
Configure Bacula-Web
====================

From Bacula-Web root folder, copy the file config.php.sample as below

*Please note that since version 5.1.0, the config file is a PHP script.*

::

   # cd application/config
   # cp -v config.php.sample config.php
   # chown -v apache: config.php

Languages
---------

Bacula-Web have been translated in different languages (thumbs up to all the contributors for their help).

   * English (default)
   * Spanish (last update by Juan Luis Francés Jiménez)
   * Italian (last update by Gian Domenico Messina (gianni.messina AT c-ict.it)
   * French (last update by Morgan LEFIEUX - comete AT daknet.org)
   * German (last update by Florian Heigl)
   * Swedish - Maintened by Daniel Nylander (po@danielnylander.se)
   * Portuguese Brazil - Last updated by J. Ritter (condector@gmail.com)

To change the default displayed language, modify the option in config.php (see below)

::

   $config['language'] = 'en_EN'; // (default language)
                             
   // Other available languages
                             
   // en_US (or en_UK)
   // es_ES
   // it_IT
   // fr_FR
   // de_DE
   // sv_SV
   // pt_BR

Options

Hide empty pools & Show_inactive_clients

As of version 5.2.11, the configuration file contain these two new options

::

   // Show inactive clients (hidden by default)
   $config['show_inactive_clients'] = true;
                             
   // Hide empty pools (displayed by default)
   $config['hide_empty_pools'] = false;

Custom date/time format

Since version 7.4.0

::

   // Custom datetime format (by default: Y-m-d H:i:s)
   // Examples
   // $config['datetime_format'] = 'd/m/Y H:i:s';
   // $config['datetime_format'] = 'm-d-Y H:i:s';

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
