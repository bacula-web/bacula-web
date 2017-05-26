.. _install/upgrade

=======
Upgrade
=======

Upgrading Bacula-Web installation is very easy, but you need to have at least basic linux administration skills.

Backup the config file
----------------------

Make a copy of the config file which is located under the folder

::

   File: application/config/config.php

   # cp -v application/config/config.php $HOME/ 

Check the requirements
----------------------

Ensure that you meet all system requirements (more informations in the :ref:`install/requirements` page).

Empty the current folder
------------------------

On Centos / Fedora / RHEL

::

   # rm -rfv /var/www/html/bacula-web/*
 
On Debian / Ubuntu

::

   # rm -rfv /var/www/bacula-web


Then, proceed with the upgrade by downloading the new archive (read the :ref:`install/download` chapter)

Once you are done, copy Bacula-Web config file back to the right folder

::

   # cp -v $HOME/config.php application/config/
 
On Centos / Fedora / RHEL

::

   # chown -v apache: application/config/config.php
 
On Debian / Ubuntu

::

   # chown -v www-data: appilcation/config/config.php

Test your setup
---------------

Test the installation using the test page (example of url below)

::

   http://yourserver/bacula-web/test.php
