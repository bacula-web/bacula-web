.. _install/index:

============
Installation
============

.. toctree::
   :maxdepth: 2

Before you start
================

Before starting the installation of Bacula-Web, please you meet the requirements listed below

   * you have access to the server using ssh or console access
   * you have root access or sudo privileges

.. important:: Installing Bacula-Web using root account is not recommended, use a regular account with sudo privileges

Installation
============

Bacula-Web can be installed in different way, but the recommended way is by using Composer

You will find more details by following the links below

   * Install :ref:`using Composer<install/installcomposer>`
   * Install :ref:`using the archive<install/installarchive>` (see note below)

As of version version 8.7.0, Bacula-Web can also be run using **Docker container image** (follow link below for more details)

   * Install using Docker (more details https://hub.docker.com/r/baculaweb/bacula-web)

.. important:: Composer archive installation will be deprecated soon, more installation options will come at the same time

Make sure your system meets the minimal requirements by checking the :ref:`requirement <install/requirements>` page

Web server
==========

You can pick one of the web server listed below to install Bacula-Web on your server

   * :ref:`Apache web server installation<install/apache-installation>`
   * :ref:`Nginx web server installation<install/installnginx>`
   * :ref:`Lighttpd web server installation<install/lighttpd-installation>`

Configuration
=============

Once Bacula-Web web application is installed, follow :ref:`these instructions <install/configure>` to finalize the configuration

.. important:: **Important:** If you have not disabled user authentication, **you must** :ref:`follow the final steps<install/finalize>` which will setup the user authentication database for you

Test
====

To make sure your setup is in good shape, follow instructions in the :ref:`test chapter<install/test>`

Upgrade
=======

Upgrading Bacula-Web is documented in the :ref:`upgrade <install/upgrade>` chapter