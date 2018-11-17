.. _install/upgrade:

=======
Upgrade
=======

Upgrading Bacula-Web installation is very easy, you only need basic linux administration skills.

Backup your setup first 
=======================

Do a copy of the config file and users database

::

   File: application/config/config.php
   File: application/assets/protected/application.db

   # cp -pv application/config/config.php $HOME/ 
   # cp -pv application/assets/protected/application.db $HOME/

Check the requirements
======================

Ensure that you meet all system requirements (more informations in the :ref:`install/requirements` page).

Empty the current folder
========================

On Centos / Fedora / RHEL

::

   # rm -rfv /var/www/html/bacula-web/*
 
On Debian / Ubuntu

::

   # rm -rfv /var/www/bacula-web


Then, proceed with the upgrade by downloading the new archive (read the :ref:`install/installarchive` chapter)

Once you are done, copy Bacula-Web config file and users database back to their original location

::

   # cp -pv $HOME/config.php <install folder>/application/config/
   # cp -pv $HOME/application.db <install folder>/application/assets/protected/

Fix files ownership
===================

**Red Hat / Centos / Fedora**

::

   # chown -v apache: <install folder>/application/config/config.php
   # chown -Rv apache: <install folder>/application/assets/protected

**Debian / Ubuntu**

::

    # chown -v www-data: <install folder>/application/config/config.php
    # chown -Rv www-data: <install folder>/application/assets/protected

Test your setup
===============

It's now time to :ref:`install/test`
