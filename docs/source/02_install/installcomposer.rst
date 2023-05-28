.. _install/installcomposer:

=================================
Install Bacula-Web using Composer
=================================

Since version 8.0.0, Bacula-Web dependencies are managed using `Composer`_.

As stated on `Composer's official website <https://getcomposer.org/doc/00-intro.md#dependency-management>`_

*Composer is a tool for dependency management in PHP. 
It allows you to declare the libraries your project depends on and it will manage (install/update) them for you.*

Let's start by installing Composer on your system

Install Composer
================

Most Linux distro provides Composer as package, so to install it run this command

::

    On Debian / Ubuntu
    $ sudo apt-get install composer

    On Red Hat, Centos, Fedora
    $ sudo yum install composer

If your distro doesn't provide Composer package, Composer website contains all information
you can install it manually as explained on `this page <https://getcomposer.org/download/>`_.

.. important::

   Make sure you're using latest Composer version. Read more on `Packagist.com website <https://getcomposer.org/2>`_

Use Composer to install Bacula-Web 
==================================

From your $HOME folder, run the command below

::

    $ composer create-project --no-dev --prefer-dist bacula-web/bacula-web bacula-web

Once done, you can check Bacula-Web installation by running the command below

::

    $ cd bacula-web && composer check

    Checking platform requirements for packages in the vendor dir
    ext-dom       20031129   success
    ext-gettext   8.0.28     success
    ext-json      8.0.28     success
    ext-libxml    8.0.28     success
    ext-mbstring  *          success provided by symfony/polyfill-mbstring
    ext-openssl   8.0.28     success
    ext-pcre      8.0.28     success
    ext-pdo       8.0.28     success
    ext-phar      8.0.28     success
    ext-posix     8.0.28     success
    ext-simplexml 8.0.28     success
    ext-sqlite3   8.0.28     success
    ext-tokenizer 8.0.28     success
    ext-xml       8.0.28     success
    ext-xmlwriter 8.0.28     success
    php           8.0.28     success

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
