.. _install/index:

============
Installation
============

This section all the information you'll need in order to install, configure, test and upgrade Bacula-Web on your system.

Introduction
============

Bacula-Web is a web application written in PHP and should be run on Apache httpd server (Nginx works fine too).

Before starting the installation of Bacula-Web, please make sure you have a valid ssh or console access to your server.

.. important:: Ability to run shell commands as root or using sudo is also a requirement.

Installation overview
=====================

Bacula-Web installation consists only in few steps (see below)

+----------------------+-----------------------------------------------------------------------------------------------------------------+
| Step                 | Instructions                                                                                                    |
+======================+=========================+==================+====================================================================+
| Requirements         | Ensure your server met all :ref:`requirements <install/requirements>`                                           |
+----------------------+-----------------------------------------------------------------------------------------------------------------+
| Web server setup     | :ref:`Setup <install/install>` and :ref:`configure <install/configwebserver>` Apache web server                 |
+----------------------+-----------------------------------------------------------------------------------------------------------------+
| (optional)           | :ref:`Setup and configure <install/installnginx>` Nginx web server                                              |
+----------------------+-----------------------------------------------------------------------------------------------------------------+

Installation
------------

You have two different options to install Bacula-Web

+----------------------+---------------------------------------------------------------------------+
| Install options      | Instructions                                                              |
+======================+==================+========================================================+
| From archive         | Instruction to :ref:`install/installarchive`                              |
+----------------------+---------------------------------------------------------------------------+
| or                                                                                               |
+----------------------+---------------------------------------------------------------------------+
| Using Composer       | Instruction to :ref:`install/installcomposer`                             |
+----------------------+---------------------------------------------------------------------------+

Ugrade
------

+----------------------+---------------------------------------------------------------------------+
| Upgrade              | Instruction to :ref:`install/upgrade` Bacula-Web                          |
+----------------------+---------------------------------------------------------------------------+

Final steps
-----------

+----------------------+---------------------------------------------------------------------------+
| Configure            | :ref:`install/configure` for your environment                             |
+----------------------+---------------------------------------------------------------------------+
| Check your setup     | :ref:`install/test` and make sure your setup is ok                        |
+----------------------+---------------------------------------------------------------------------+
| Finalize your setup  | :ref:`install/finalize` (required step)                                   |
+----------------------+---------------------------------------------------------------------------+
