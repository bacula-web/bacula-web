.. _install/index:

#####################
Installation overview
#####################

Before starting the installation of Bacula-Web, please you meet the requirements listed below

   * you have access to the server using ssh or console access
   * you have root access or sudo privileges

.. important:: Installing Bacula-Web using root account is not recommended, use a regular account with sudo privileges

Bacula-Web can be installed in different way, but the recommended way is by using Composer

You will find more details by following the links below

   * Install :ref:`using Composer<install/installcomposer>`
   * Install using Docker, more details https://hub.docker.com/r/baculaweb/bacula-web (available since version 8.7.0
   * Install using the archive is not supported anymore, see note below)

.. important:: Installation using the archive which used to be available on GitHub releases is not supported anymore due to several issues, such as conflicts
               between different PHP versions used by community users.

Make sure your system meets the minimal requirements by checking the :ref:`requirement <install/requirements>` page

You can pick one of the :ref:`web server <install/webserver>` listed below to install Bacula-Web on your server

   * :ref:`Apache web server installation<install/apache-installation>`
   * :ref:`Nginx web server installation<install/installnginx>`
   * :ref:`Lighttpd web server installation<install/lighttpd-installation>`

Once Bacula-Web web application is installed, follow :ref:`these instructions <install/configure>` to finalize the configuration

**Important:** If you have not disabled user authentication, **YOU MUST** :ref:`follow the final steps<install/finalize>` which will setup the user authentication database for you

To make sure your setup is in good shape, follow instructions in the :ref:`test chapter<install/test>`

.. toctree::
   :maxdepth: 2

   requirements
   installcomposer
   webserver-setup
   configure
   finalize
   test
