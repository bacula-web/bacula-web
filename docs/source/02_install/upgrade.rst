.. _install/upgrade:

*******
Upgrade
*******

Upgrading Bacula-Web installation is very easy, you only need basic linux administration skills.

Configuration backup
====================

Before proceeding to the upgrade, make sure you do a copy of the config file and users database

::

   File: <bacula-web path>/application/config/config.php
   File: <bacula-web path>/application/assets/protected/application.db

   # cp -pv <bacula-web path>/application/config/config.php $HOME/
   # cp -pv <bacula-web path>/application/assets/protected/application.db $HOME/

Check the requirements
======================

Ensure that you meet all system requirements (more information in the :ref:`install/requirements` page).

Using Composer
==============

Please use steps described below to upgrade Bacula-Web to latest stable version using Composer

Move to Apache root folder 

**Red Hat / Centos / Fedora**

::

    $ cd /var/www/html 
    $ sudo mv -v bacula-web bacula-web-beforeupgrade

.. note:: The path might need to be adapted depending on your setup

Get latest stable version of Bacula-Web

**Red Hat / Centos / Fedora**

::

    $ sudo -u apache composer create-project bacula-web/bacula-web bacula-web @stable

**Debian / Ubuntu**

::

    $ sudo -u www-data composer create-project bacula-web/bacula-web bacula-web @stable

Copy configuration and users database to new Bacula-Web folder

::

    $ sudo cp -pv bacula-web-beforeupgrade/application/config/config.php bacula-web/application/config/
    $ sudo cp -pv bacula-web-beforeupgrade/application/assets/protected/* bacula-web/application/assets/protected/

Fix files ownership and permissions
===================================

::

    $ sudo mv -v bacula-web /var/www/
    $ sudo chown -Rv www-data: /var/www/bacula-web
    $ sudo chmod -Rv 755 /var/www/bacula-web
    $ sudo chmod -v 775 /var/www/bacula-web/application/views/cache
    $ sudo chmod -v 775 /var/www/bacula-web/application/assets/protected

.. important::

             Above instructions are based on Debian/Ubuntu distro.

             On rpm based distro, change the user from www-data to **apache**, in case od doubts, please refer to the OS documentation.

             If you've installed Bacula-Web somewhere else than **/var/www/bacula-web**, then you'll need to adapt to your setup.

Test your setup
===============

Once the upgrade process is completed, It is time to :ref:`test your Bacula-Web installation <install/test>`
