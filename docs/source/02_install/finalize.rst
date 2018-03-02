.. _install/finalize:

==============================
Finalize your setup
==============================

Change admin password
---------------------

.. image:: /_static/bacula-web-user-settings-menu.jpg
   :scale: 30 %
   :align: right

Starting from version 8.0.0, users informations are stored in SQLite database. This database is created automatically after installation.

The default user is **admin**, with the password **bacula**.

I strongly recommend you to change the password as soon as possible.

The password can be changed very easily by using the **User settings** menu at the top of the page.

.. note:: The users database is stored in <install folder>/application/assets/protected/application.db

Simply use **Password management** form to reset current user password

.. image:: /_static/bacula-web-user-settings.jpg
   :scale: 60 %

Manage users
------------

.. image:: /_static/bacula-web-settings-menu.jpg
   :scale: 30 %
   :align: right

You can manage users from the **Genral settings** dropdown menu.

To add more users, simply use the **Add user** form at the bottom of the page (password must be at least 6 characters long).

.. info:: User's email address is not for the moment but it will in a future version

.. image:: /_static/bacula-web-users.jpg
   :scale: 60%
