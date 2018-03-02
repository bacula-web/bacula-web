.. _install/index:

Installation
============

This section all the information you'll need in order to install, configure, test and upgrade Bacula-Web on your system.

Introduction
------------

Bacula-Web is a web application written in PHP and should be run on Apache httpd server (Nginx works fine too).

Before starting the installation of Bacula-Web, please make sure you have a valid ssh or console access to your server.

.. important:: Ability to run shell commands as root or using sudo is also a requirement.

Installation overview
---------------------

Bacula-Web installation consists only in few steps (see below)

+----------------------+---------------------------------------------------------------------------+
| Step                 | Instructions                                                              |
+======================+==================+========================================================+
| Requirements         | Ensure your server met all :ref:`install/requirements`                    |
+----------------------+---------------------------------------------------------------------------+
| Web server setup     | :ref:`install/install` and :ref:`install/configwebserver` your web server |
+----------------------+---------------------------------------------------------------------------+

You have two different options to install Bacula-Web

+----------------------+---------------------------------------------------------------------------+
| Install options      | Instructions                                                              |
+======================+==================+========================================================+
| From archive         | Instruction to :ref:`install/installarchive`                              |
+----------------------+---------------------------------------------------------------------------+
| Using Composer       | Instruction to :ref:`install/installcomposer`                             |
+----------------------+---------------------------------------------------------------------------+

Final steps

+----------------------+---------------------------------------------------------------------------+
| Configure            | :ref:`install/configure` for your environment                             |
+----------------------+---------------------------------------------------------------------------+
| Check your setup     | :ref:`install/test` and make sure your setup is ok                        |
+----------------------+---------------------------------------------------------------------------+
| Finalize your setup  | :ref:`install/finalize`                                                   |
+----------------------+---------------------------------------------------------------------------+

Installation / upgrade instructions
-----------------------------------

.. toctree::
   :maxdepth: 2

   requirements
   install
   configwebserver
   installarchive
   installcomposer
   configure
   test
   upgrade
