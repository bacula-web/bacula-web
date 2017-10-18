.. _install/index:

Installation
============

This section all the information you'll need in order to install, configure, test and upgrade Bacula-Web on your system.

Introduction
------------

Bacula-Web is a web application written in PHP and should be run on Apache httpd server (Nginx works fine too).

Before starting the installation of Bacula-Web on your server, please make sure you have access to through ssh or console.

Ability to run shell commands as root or using sudo is also a requirement.

Installation overview
---------------------

Bacula-Web installation consists only in few steps (see below)

+----------------------+---------------------------------------------------------------------------+
| Step                 | Instructions                                                              |
+=========================================+========================================================+
| Requirements         | Ensure your server met all :ref:`install/requirements`                    |
+----------------------+---------------------------------------------------------------------------+
| Web server setup     | :ref:`install/install` and :ref:`install/configwebserver` your web server |
+----------------------+---------------------------------------------------------------------------+
| Download             | :ref:`install/download` Bacula-Web pre-built package                      |
+----------------------+---------------------------------------------------------------------------+
| Install dependencies | Install Bacula-Web dependencies                                           |
+----------------------+---------------------------------------------------------------------------+
| Configure            | Configure Bacula-Web for your environment                                 |
+----------------------+---------------------------------------------------------------------------+
| Check your setup     | Make sure your Bacula-Web setup is ok                                     |
+----------------------+---------------------------------------------------------------------------+

Installation / upgrade instructions
-----------------------------------

.. toctree::
   :maxdepth: 2

   requirements
   configwebserver
   download
   install
   configure
   upgrade
   test
