.. _install/index:

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
   * Install using Docker, more details https://hub.docker.com/r/baculaweb/bacula-web (available since version 8.7.0
   * Install using the archive is not supported anymore, see note below)

.. important:: Installation using the archive which used to be available on GitHub releases is not supported anymore due to several issues, such as conflicts
               between different PHP versions used by community users.

Make sure your system meets the minimal requirements by checking the :ref:`requirement <install/requirements>` page

Web server setup
================

You can pick one of the web server listed below to install Bacula-Web on your server

   * :ref:`Apache web server installation<install/apache-installation>`
   * :ref:`Nginx web server installation<install/installnginx>`
   * :ref:`Lighttpd web server installation<install/lighttpd-installation>`

Configuration
=============

Once Bacula-Web web application is installed, follow :ref:`these instructions <install/configure>` to finalize the configuration

Finalize setup
==============

**Important:** If you have not disabled user authentication, **YOU MUST** :ref:`follow the final steps<install/finalize>` which will setup the user authentication database for you

Test
====

To make sure your setup is in good shape, follow instructions in the :ref:`test chapter<install/test>`

Upgrade
=======

Upgrading Bacula-Web is documented in the :ref:`upgrade <install/upgrade>` chapter