.. _install/test:

===============
Test Bacula-Web
===============

After installing/upgrading and configuring Bacula-Web, just ensure that Bacula-Web will work fine.

A test page exist for this purpose that check the following items

   * required package are succesfully installed
   * smarty template cache good permissions
   * application database back-end good permissions
   * php modules are installed and properly configured

To test your installation of Bacula-Web, follow this link

::

   http://yourserveroripaddress/bacula-web/index.php?page=test

You should got the same result as shown in the screenshot below

.. image:: /_static/bacula-web-test-page.jpg
   :scale: 20%

.. note:: Default username and password are (admin/bacula)
