# Configure

This section explain how to create the configuration file which contain your custom settings.

:::info
Please note that since version 5.1.0, the config file is a PHP script (it was a .conf file before this version).
:::

## Create configuration file

Bacula-Web settings are stored in the file `application/config/config.php`

From Bacula-Web root folder, copy the sample config file and give it the name **config.php**

```shell
$ sudo cd application/config
$ sudo cp -v config.php.sample config.php
```

Now you need to make sure config.php has the correct permissions.

The configuration file needs to be at least readable by the user owning the web server process.

On **Red Hat / Centos / Fedora**, the user running Apache is **apache**

On **Debian / Ubuntu**, the user running Apache is **www-data**

So depending on which system you have installed Bacula-Web, run the command below

```shell
$ sudo chown -v <apache_user>: config.php
```

For example, on Debian Jessie, use this command

```shell
$ sudo chown -v www-data: config.php
```

on Centos 7

```shell
$ sudo chown -v apache: config.php
```

:::tip
Do not forget the column `:` after apache_user
:::

## General settings

| Setting                          | Description                               | Default value                        |
|----------------------------------|-------------------------------------------|--------------------------------------|
| $config['language']              | Set displayed language                    | en_US                                |
| $config['hide_empty_pools']      | Hide empty pools                          | true                                 |
| $config['show_inactive_clients'] | Show inactive clients or not              | true                                 |
| $config['datetime_format']       | Change default date and time format       | Y-m-d H:i:s                          |
| $config['datetime_format_short'] | Change default short date and time format | Generated from above datetime_format |
| $config['enable_users_auth']     | Enable or disable users authentication    | true                                 |
| $config['rows_per_page']         | Rows per pagination page                  | 25                                   |
| $config['debug']                 | Enable or disable debug mode              | false                                |
| $config['basepath']              | Set base path                             | empty                                |


### language

**Description**

As Bacula-Web is translated into not less than 15 languages, the language setting allow you to change the displayed
language of Bacula-Web.

You can simply change from the default english (en_US), to your language by setting the $config['language'] parameter value.

**Example**

```php
$config['language'] = 'pt_BR'; // For portuguese brazilian
```

:::tip
Important note

Before changing default language, please make sure the locale is available.
To verify this you can run this command on the server running Bacula-Web.

```shell
$ locale -a
```

also make sure you've restarted the web server service.
:::

### hide_empty_pools

**Description**

Do not display empty pools in Dashboard (Pools and volumes widget)

**Example**

```php
$config['hide_empty_pools'] = false;
```

:::note
This setting is available since Bacula-Web 5.2.11
:::

### show_inactive_clients

**Description**

If disabled (set to *false*), don't list or show inactive clients

**Example**

```php
$config['show_inactive_clients'] = true;
```

:::note
This setting is available since Bacula-Web 5.2.11
:::


### datetime_format

**Description**

Define your custom date & time format (by default Y-m-d H:i:s)

For more information on date format, have a look on date() function in [PHP manual](http://php.net/manual/en/function.date.php)

**Example**

```php
$config['datetime_format'] = 'd/m/Y H:i:s';
```

or

```php
$config['datetime_format'] = 'm-d-Y H:i:s';
```

:::note
This setting is available only since version 7.4.0
:::

### datetime_format_short

**description**

Define your custom short date & time format

This config parameter is **optional** (commented out by default in config.php.sample)
The default value is generated from datetime_format config parameter.

For more information on date format, have a look on date() function in [PHP manual](http://php.net/manual/en/function.date.php)

**Example**

```php
$config['datetime_format_short'] = 'd/m/Y';
```

### enable_users_auth

**Description**

Enable or disable users authentication.

This settings is useful if you already authenticate users on Web server side, using .htpasswd
or LDAP authentication (mod_auth_ldap or any other).

**Example**

```php
// By default, users authentication is enabled
$config['enable_users_auth'] = true;

// Disable it using config below
$config['enable_users_auth'] = false;
```

:::danger
Use this setting with caution, don't disable users authentication unless you already authenticated users.
:::

### rows_per_page

**Description**

Define how many rows per pagination page will be displayed.

**Example**

```php
$config['rows_per_page'] = 25;
```

:::note
This setting is available since version 8.5.0
:::

### debug

**Description**

Enable or disable debug mode

Debug mode could be helpful to troubleshoot Bacula-Web setup problem. Debug mode is disabled by default

**Example**

To enable debug mode

```php
$config['debug'] = true;
```

:::danger
Use debug mode setting with caution, sensitive information can be disclosed if your Bacula-Web setup is exposed to unsecure network.
:::

### basepath

**Description**

Uncomment this config parameter **only** if you are using Apache mod_alias and have setup Bacula-Web in a sub-folder (eg: http://yourhost/bacula-web)

This config parameter is commented by default in config.php.sample, comment it out only if needed.

**Example**

```php
$config['basepath'] = '/bacula-web';
```

:::tip
Do not forget to prefix `$config['basepath']` value with a `/`
:::


:::note
This setting is available since version 9.1.0
:::

## Database connection settings

Each Bacula catalog (database) needs to be defined using the settings below

| Setting  | Description                                       | Example                                |
|----------|---------------------------------------------------|----------------------------------------|
| label    | label displayed in the catalog drop-down selector | Backup server                          |
| host     | hostname of the db server hosting Bacula catalog  | localhost, fqdn host or ip address     |
| db_name  | name of the catalog database name                 | usually bacula, unless you changed it  |
| login    | database user                                     | bacula, admin, etc.                    |
| password | database password                                 | mK3DQLolUV                             |
| db_type  | database type                                     | mysql, pgsql or sqlite                 |
| db_port  | database port number                              | Default port for MySQL/MariaDB is 3306 |
|          |                                                   | Default port for postgreSQL is 5432    |
|          |                                                   | Leave blank for SQLite                 |

:::tip
Please read the Bacula [database user/role](../admin-guides/bacula-database-user) page for further instructions for MySQL/MariaDB and postgreSQL.
:::

**Examples**

### Single MySQL Bacula catalog

Truncated `config.php` file

```php
$config[0]['label'] = 'Backup Server'; // Bacula catalog label (used for catalog selector)
$config[0]['host'] = 'localhost';  // database server hostname
$config[0]['db_name'] = 'bacula'; // Bacula database name
$config[0]['login'] = 'bacula'; // Database user
$config[0]['password'] = 'verystrongpassword'; // Database user's password
$config[0]['db_type'] = 'mysql'; // Database type (mysql || pgsql || sqlite)
$config[0]['db_port'] = '3306'; // Database port
```

### Multiple catalogs (example)

Truncated `config.php` file

```php
   <?php
   ...
   //MySQL bacula catalog
   $config[0]['label'] = 'Backup Server';
   $config[0]['host'] = 'localhost';
   $config[0]['login'] = 'bacula';
   $config[0]['password'] = 'verystrongpassword';
   $config[0]['db_name'] = 'bacula';
   $config[0]['db_type'] = 'mysql';
   $config[0]['db_port'] = '3306';

   //PostgreSQL Lab server
   $config[1]['label'] = 'Lab backup server';
   $config[1]['host'] = '192.168.0.120';
   $config[1]['login'] = 'bacula';
   $config[1]['password'] = 'verystrongpassword';
   $config[1]['db_name'] = 'bacula';
   $config[1]['db_type'] = 'pgsql';
   $config[1]['db_port'] = '5432';
   ?>
```

### Full configuration example

```php
   <?php
   $config[0]['language'] = 'en_US';

   $config['show_inactive_clients'] = false;

   $config['hide_empty_pools'] = true;

   // MySQL bacula catalog
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
```

:::warning
If you define several Bacula catalog, make sure each catalog connection settings have a different id

Example: 

```php
<?php

$config[0]['label'] = 'Bacula Dir X';
$config[0]['host'] = '...';
$config[0]['login'] = '...';
$config[0]['password'] = '...';
...

$config[1]['label'] = 'Bacula Dir Y';
$config[1]['host'] = '...';
$config[1]['login'] = '...';
$config[1]['password'] = '...';
...
```
:::
