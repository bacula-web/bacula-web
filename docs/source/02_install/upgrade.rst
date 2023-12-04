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
