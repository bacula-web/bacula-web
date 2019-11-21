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

::

   # curl -O -L https://github.com/bacula-web/bacula-web/releases/download/v8.3.2/bacula-web-8.3.2.tgz

Verify archive signature (optional)
===================================

Download sha256 or sha512 signature file from the download page, and run one of these command to verify the signature

::

   # cat sha256sum.txt | sha256sum -c

   or

   # cat sha512sum.txt | sha512sum -c

Decompress the archive
======================

**On Red Hat / Centos / Fedora**

::

   # tar xvf bacula-web-latest.tgz
 
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

It's now time to :ref:`install/configure`
