.. _install/installarchive:

===============================
Install Bacula-Web from archive
===============================

Download the source tarball
===========================

Move into Apache directory

On RedHat / Centos

::

   # cd /var/www/html
 
On Debian / Ubuntu

::
   
   # cd /var/www/

and download the archive by running this command

::

   # curl -O -L https://github.com/bacula-web/bacula-web/releases/download/v8.6.3/bacula-web-8.6.3.tgz

.. important:: Since Bacula-Web version 8.3.2, compressed archive and signature files are available in `GitHub releases`_.

Verify archive signature (optional)
===================================

Both SHA 256 and 512 signature files are available from Bacula-Web project release page

I'd strongly suggest to verify the integrity of the compressed archive you downloaded before proceeding to the installation.

**SHA256**
`https://github.com/bacula-web/bacula-web/releases/download/v8.6.3/sha256sum.txt <https://github.com/bacula-web/bacula-web/releases/download/v8.6.3/sha256sum.txt>`_

**SHA512**
`https://github.com/bacula-web/bacula-web/releases/download/v8.6.3/sha512sum.txt <https://github.com/bacula-web/bacula-web/releases/download/v8.6.3/sha512sum.txt>`_

Download the SHA sum file using the link above (adapt the version, the links are just examples).

Once downloaded, run one the command below from the same path where you've downloaded the bacula-web-<version>.tgz

::

   # cat sha256sum.txt | sha256sum -c

   or

   # cat sha512sum.txt | sha512sum -c

Both command should output something like below

::

  bacula-web-8.6.3.tgz: OK

Decompress the archive
======================

**On Red Hat / Centos / Fedora**

::

   # tar -xvf bacula-web-latest.tgz
 
**On Debian / Ubuntu**

::

   # tar xvf bacula-web-latest.tgz

Change files/folders permissions
================================

**On Centos / Red Hat / RHEL**

::

   # chown -Rv apache: /var/www/html/bacula-web
 
**On Debian / Ubuntu**

::

   $ sudo chown -Rv www-data: /var/www/bacula-web
   $ sudo chmod -Rv 755 /var/www/bacula-web
   $ sudo chmod -v 775 /var/www/bacula-web/application/views/cache
   $ sudo chmod -v 775 /var/www/bacula-web/application/assets/protected

.. note:: For those who are using php-fpm, or any other web server stack, the basic idea to ensure Bacula-Web will properly, is to give the right ownership and permission to the cache folder. So make sure the user running the web server process is the owner of the **application/views/cache** folder, or have enough permissions to write into this folder. 

It's now time to :ref:`configure Bacula-Web <install/configure>`

.. _GitHub releases: https://github.com/bacula-web/bacula-web/releases