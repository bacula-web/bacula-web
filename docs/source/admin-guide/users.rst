.. _admin-guide/users:

********************
Users administration
********************

Users administration

Create a user
=============

Users can be created from the web interface, or using the bwc console tool

Using the web interface
-----------------------

Add some content and screenshot here

Using the bwc console tool
--------------------------

Create an admin user

::

   $ php bwc app:user-create --role=admin admin

   Email address: admin@example.com
   Password:


    [OK] User admin successfully created

Create a standard user

::

   $ php bwc app:user-create johndoe

   Email address: jdoe@example.com
   Password:

    [OK] User johndoe successfully created

.. note::

   You can use ``--role`` to specify the user role (default user role is regular user)

   For more options, use -h

   ::

     $ bwc app:user-create -h

Reset a user's password
=======================

Using the CLI
-------------

Using Web UI
------------

Delete a user
=============

Using the Web UI
----------------

As an administrator, go to ....

Using the CLI
-------------

From a command prompt on the server running Bacula-Web, run

::

   $ php bwc app:user-delete johndoe

Change a user role
==================

Using the Web UI
----------------

As an administrator, go to ....

.. caution:: You **must** have the **admin** role in order to change other user's roles

Using the CLI
-------------

From a command prompt on the server running Bacula-Web, run

::

   $ php bwc app:user-set-role admin johndoe

Above command set the *johndoe* user the *admin* role
