.. _install/test:

===============
Test Bacula-Web
===============

After installing/upgrading and configuring Bacula-Web, just ensure that Bacula-Web will work fine.

Using the test page
===================

A test page exist for this purpose that check the following items

   * required package are successfully installed
   * Twig cache folder good permissions
   * application database back-end good permissions
   * php modules are installed and properly configured

To test your installation of Bacula-Web, follow this link

::

   http://yourserveroripaddress/bacula-web/test

You should got the same result as shown in the screenshot below

.. image:: /_static/bacula-web-test-page.jpg
   :scale: 20%

Using Bacula-Web console
========================

Bacula-Web console is a PHP script which can be run from the command line only.

This script verifies if you've installed all required dependencies and make sure everything has been configured correctly
to run Bacula-Web.

Open a shell command prompt and move to Bacula-Web installation folder

::
   
   $ cd /var/www/html/bacula-web

   # On Debian/Ubuntu
   $ sudo -u www-data php bwc check

   On Red Hat, Fedora, etc.
   $ sudo -u apache php bwc check

.. note:: Bacula-Web console is available since version 8.1.0
