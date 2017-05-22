.. _install/download:

========
Download
========

Move into Apache directory

On RedHat / Centos

::

   # cd /var/www/html
 
On Debian / Ubuntu

::
   
   # cd /var/www/

Download the source tarball
---------------------------

::

   # curl -O http://www.bacula-web.org/files/bacula-web.org/downloads/bacula-web-latest.tgz

Verify archive signature (optional)
----------------------------------

Download sha256 or sha512 signature file from the download page, and run one of these command to verify the signature

::

   # cat sha256sum.txt | sha256sum -c

   or

   # cat sha512sum.txt | sha512sum -c

Create Bacula-Web folder
------------------------

**On Centos / Fedora / RHEL**

::

   # mkdir -v /var/www/html/bacula-web
 
**On Debian / Ubuntu**

::

   # mkdir -v /var/www/bacula-web

Decompress the archive
----------------------

**On Red Hat / Centos / Fedora**

::

   # tar -xzf bacula-web-latest.tgz -C /var/www/html/bacula-web
 
**On Debian / Ubuntu**

::

   # tar -xzf bacula-web-latest.tgz -C /var/www/bacula-web

Change files/folders permissions
--------------------------------

**On Centos / Red Hat / RHEL**

::

   # chown -Rv apache: /var/www/html/bacula-web
 
**On Debian / Ubuntu**

::

   # chown -Rv www-data: /var/www/bacula-web
   # chmod -Rv u=rx,g=rx,o=rx /var/www/bacula-web
