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

Most Linux distro povides Composer as package, so to install it run this command

::

    On Debian / Ubuntu
    $ sudo apt-get install composer

    On Red Hat, Centos, Fedora
    $ sudo yum install composer

If your distro doesn't provide Composer package, you can install Composer manually

Open a shell (as root) on your server and run these command

::

    # cd /usr/local/bin/
    # curl -sS https://getcomposer.org/installer | php
    # mv composer.phar composer

Make sure $PATH contain /usr/loca/bin

::

    $ echo $PATH

If it's not the case, fix it

::

    $ export PATH=$PATH:/usr/local/bin

For more detailled instructions, check `this page <https://getcomposer.org/download/>`_.

.. warning:: Never use composer as a super-user or root, use the web server user or the one who own Bacula-Web files and folders

**********************************
Use Composer to install Bacula-Web 
**********************************

From your $HOME folder, run the command below

::

    $ composer create-project --prefer-dist bacula-web/bacula-web bacula-web

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
