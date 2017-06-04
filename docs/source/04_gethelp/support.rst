.. _gethelp/support:

#########################
Bugs and features request
#########################

***************
How to get help
***************

If you're in trouble while installing, upgrading, configuring or using Bacula-Web ?
No problem, feel free to register and submit a bug report and features request in the dedicated `bug tracker`_.

.. _bug tracker: http://bugs.bacula-web.org

********************
Bug report guideline
********************

Before submiting a bug report, please make sure that ...

  * check the :ref:`FAQ page <gethelp/faq>`, there might be some useful informations which can help you
  * you're using the latest version of Bacula-Web
  * the config file have been adapted to your configuration (check the documentation)
  * all items are ok in the test page (except for the database engine you don't use)
  * you checked Apache error log file for some warnings or errors
  * you're able to connect to the database with the client

    * $ mysql for MySQL
    * $ psql for postgreSQL
    * $ sqlite3 for SQLite

Then make sure you include those details in the bug report

  * PHP version
  * Which database you're using (SQLite, MySQL or postgreSQL)
  * Apache (or any web server) logs
  * Screenshot(s) are useful too

As much details you provide, as fast I will be able to help you and fix the bug (if any)
