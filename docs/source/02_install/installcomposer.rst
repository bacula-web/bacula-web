.. _install/installcomposer:

#################################
Install Bacula-Web using Composer
#################################

Since version 8.0.0, Bacula-Web dependencies management is performed using `Composer`_.

As stated on `Composer's official website <https://getcomposer.org/doc/00-intro.md#dependency-management>`_, 

*Composer is a tool for dependency management in PHP. 
It allows you to declare the libraries your project depends on and it will manage (install/update) them for you.*

Let's start by installing Composer on your system

****************
Install Composer
****************

To install Composer, open a shell on your server and run these command

Move in Bacula-Web root folder (adapt the command line to your setup)

:: 

    $ cd /var/www/html/bacula-web
    $ curl -sS https://getcomposer.org/installer | php

If you want to install composer in a globaly available path, I suggest to install it like this

::

    $ cd /usr/local/bin/
    $ curl -sS https://getcomposer.org/installer | php


For more details, please have a look on `this page <https://getcomposer.org/download/>`_.

.. warning:: Never use composer as a super-user or root, use the web server user or the one who own Bacula-Web files and folders

**********************************
Use Composer to install Bacula-Web 
**********************************

From your $HOME folder, run the command below

::

    $ composer create-project --prefer-dist bacula-web/bacula-web:*@RC bacula-web

Fix files/folders ownership and permissions

On Centos / Red Hat / Fedora

::

    $ sudo mv -v bacula-web /var/www/html/
    $ sudo chown -Rv apache: /var/www/html/bacula-web

On Debian / Ubuntu 

::

    $ sudo mv -v bacula-web /var/www/
    $ sudo chown -Rv www-data: /var/www/bacula-web
    $ sudo chmod -Rv 755 /var/www/bacula-web
    $ sudo chmod -v 775 /var/www/bacula-web/application/views/cache
    $ sudo chmod -v 775 /var/www/bacula-web/application/assets/protected

.. note:: Depending on your distro, Apache root folder can be /var/www or /var/www/html

Once you're done, it's time to :ref:`install/configure`

.. _Composer: https://getcomposer.org/ 
