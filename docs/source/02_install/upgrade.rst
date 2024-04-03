.. _install/upgrading:

#########
Upgrading
#########

Before upgrading
================

* make sure your system meets the :ref:`minimal requirements <install/requirements>`.

You can find below a summary of important changes from last supported major version can be found below.

.. _install/upgrade_from_v9_to_v10:

Upgrading from version >= v9.0.0 to v10.0.0
-------------------------------------------

**What's changed ?**

- minimal required PHP version is 7.4.0
- user authentication database has been moved from application/assets/protected to var/

.. _install/upgrade_from_v8.6_to_v9:

Upgrading from version >= v8.6.0 to v9.0.0
------------------------------------------

**What's changed ?**

- minimal supported version of PHP is 8.0.0

.. _install/upgrading_to_v8.6.0:

Upgrading from version prior v8.6.0
-----------------------------------

**What's changed ?**

- entry point script is now bacula-web/public/index.php, web server configuration MUST be updated accordingly

Changelog
=========

A more detailed `CHANGELOG <https://github.com/bacula-web/bacula-web/blob/master/docs/CHANGELOG.md>`_ is also available on the GitHub project.

***************
Upgrade process
***************

Configuration backup
====================

Before proceeding to the upgrade, make sure you do a copy of the config file and users database

::

   File: <bacula-web path>/application/config/config.php
   File: <bacula-web path>/application/assets/protected/application.db

   $ cp -pv <bacula-web path>/application/config/config.php $HOME/
   $ cp -pv <bacula-web path>/application/assets/protected/application.db $HOME/

Using Composer
==============

Please use steps described below to upgrade Bacula-Web to latest stable version using Composer

Move to Apache root folder 

**Red Hat / Centos / Fedora**

::

    $ cd /var/www/html 
    $ sudo mv -v bacula-web bacula-web-beforeupgrade

Get latest stable version of Bacula-Web

**Red Hat / Centos / Fedora**

::

    $ sudo -u apache composer create-project bacula-web/bacula-web bacula-web @stable

**Debian / Ubuntu**

::

    $ sudo -u www-data composer create-project bacula-web/bacula-web bacula-web @stable

Copy configuration and users database to new Bacula-Web folder

::

    $ sudo cp -pv $HOME/bacula-web-beforeupgrade/application/config/config.php bacula-web/config/
    $ sudo cp -pv $HOME/bacula-web-beforeupgrade/application/assets/protected/* bacula-web/var/application.db

Fix files ownership and permissions
===================================

::

    $ sudo chown -Rv www-data: /var/www/html/bacula-web
    $ sudo chmod -Rv 755 /var/www/html/bacula-web
    $ sudo chmod -v 775 /var/www/html/bacula-web/application/views/cache
    $ sudo chmod -v 775 /var/www/html/bacula-web/application/assets/protected

.. important::

   Above instructions are based on Debian/Ubuntu distro.

   On rpm based distro, change the user from www-data to **apache**, in case of doubts, please refer to the OS official documentation.

   If you've installed Bacula-Web somewhere else than **/var/www/html/bacula-web**, you'll need to adapt above paths to your setup.

Test your setup
===============

Once the upgrade process is completed, you can :ref:`test your Bacula-Web installation <install/test>`
