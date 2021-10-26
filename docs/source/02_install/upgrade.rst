.. _install/upgrade:

=======
Upgrade
=======

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

Ensure that you meet all system requirements (more informations in the :ref:`install/requirements` page).

Upgrade from archive
====================

Steps below describe how to upgrade Bacula-Web if you've used the archive available on Bacula-Web web site

Empty the current folder
------------------------

On Centos / Fedora / RHEL

::

   # rm -rfv /var/www/html/bacula-web/*
 
On Debian / Ubuntu

::

   # rm -rfv /var/www/bacula-web


Now proceed with the upgrade by downloading the new archive (read the :ref:`install/installarchive` chapter)

Once you've downloaded latest Bacula-Web archive, copy Bacula-Web configuration file and users database into latest Bacula-Web folder

::

   # cp -pv $HOME/config.php <bacula-web path>/application/config/
   # cp -pv $HOME/application.db <bacula-web path>/application/assets/protected/

Using Composer
==============

Steps below describe how to upgrade Bacula-Web if you've used Composer installation method

Upgrade using Composer
----------------------

Please use steps described below to upgrade Bacula-Web to latest stable version using Composer

Move to Apache root folder 

**Red Hat / Centos / Fedora**

::

    $ cd /var/www/html 
    $ sudo mv -v bacula-web bacula-web.beforeupgrade

..note:: The path might need to be adapted depending on your setup

Get latest stable version of Bacula-Web

**Red Hat / Centos / Fedora**

::

    $ sudo -u apache composer create-project bacula-web/bacula-web bacula-web @stable

**Debian / Ubuntu**

::

    $ sudo -u www-data composer create-project bacula-web/bacula-web bacula-web @stable

Copy configuration and users database to new Bacula-Web folder

::

    $ sudo cp -pv bacula-web.beforeupgrade/application/config/config.php bacula-web/application/config/
    $ sudo cp -pv bacula-web.beforeupgrade/application/assets/protected/* bacula-web/application/assets/protected/

Fix files ownership
===================

**Red Hat / Centos / Fedora**

::

   # chown -v apache: bacula-web/application/config/config.php
   # chown -Rv apache: bacula-web/application/assets/protected

**Debian / Ubuntu**

::

    # chown -v www-data: bacula-web/application/config/config.php
    # chown -Rv www-data: bacula-web/application/assets/protected

Test your setup
===============

Once the upgrade process is completed, It is time to :ref:`test your Bacula-Web installation <install/test>`
