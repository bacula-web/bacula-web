.. _about/features:

========
Features
========

---------------------------------------
Install once, monitor several directors
---------------------------------------

You just need to Install Bacula-Web once, then monitor as much Bacula directors you have.

Bacula-Web give you the ability to keep an eye on all your Bacula directors from a single point. Install it on a dedicated server and manage all your Bacula instances

-------------------------------------------
Keep an eye on Bacula events and ressources
-------------------------------------------

Bacula-Web Dashboard provide an overall overview of your Bacula jobs, Volumes, Pools, Catalog statistics, Amount of Bytes/Filles protected by Bacula, etc..

You can choose within predefined period like last 24 hours, last week, last month or since beginning of time period.

---------------------------
Bacula-Web in your language
---------------------------

Bacula-Web have been translated in several languages like French, German, Polish, etc.

Translations are a work in progress, if you want to contribute, please read the "How to contribute to :ref:`contribute/translations`" page

----------------
Jobs report page 
----------------

The jobs report page show you last 150 Bacula jobs with several ordering and filtering options.
Another useful option allow you to see logs for each job in the report page

--------------------
Client backup report
--------------------

Client backup report provide you for each Bacula client the details below

   * client os, client architecture, client version
   * display last known completed backup job
   * last 7 days stored bytes and files graphs

-----------------
Backup job report
-----------------

Backup job report display useful information about Bacula jobs like

   * last 7 days stored bytes and files
   * last completed jobs
   * last 7 days stored bytes and files graphs

-----------------
Pools and volumes
-----------------

Pools and volumes provide you a list of all Bacula pools and assigned volumes with details like volume name, Bytes, Media type, expiration date, last written date, status

---------
Test page
---------

Test page give you some useful informations about your Bacula-Web installation and configuration

---------
Dashboard
---------

.. image:: /_static/bacula-web-dashboard.jpg
   :scale: 20 %
   :align: right

Bacula-Web Dashboard provide a lot of informations about your Bacula infrastructure

   * Bacula Director catalog (database) statistics like
   * Catalog database current size
   * Total stored Bytes
   * Total stored files
   * Number of enabled clients
   * Used disk space by all volumes
   * Jobs status, transfered files / bytes over prefedined period (last day, last week, last month, bot aka beginning of time)
   * Volumes per pool usage graph
   * Stored bytes graph (last 7 days)
   * Stored files graph (last 7 days)
   * Last used volumes (display last 10 used volumes for backup jobs)

-----------
Jobs report
-----------

.. image:: /_static/bacula-web-jobs-report.jpg
   :scale: 20 %
   :align: right

Jobs report page display last 150 Bacula jobs in a table format.

Jobs report display latest Bacula jobs (backup,copy,restore) in a table format containing useful information like

   * Job status
   * Job ID
   * Client Name
   * Job type
   * Start, end time and elapsed time in a "human" readable format
   * Level of backup jobs (Full, Incremental, Diff)
   * Bytes and Files for backup jobs
   * Speed average for completed backup jobs
   * Compression rate
   * Pool
   * Job logs 
   * Jobs can be ordered by job id, job bytes, job files, job name, pool name
   * Jobs can filtered for a specific client or by job status

Job logs
========
   
Job logs can be displayed by clicking on the loop icon off each job (screenshot below)

.. image:: /_static/bacula-web-job-logs-option.png
   :scale: 60 %

Filter and options
==================

You can use different filter and ordering options (screenshot below)

.. image:: /_static/bacula-web-jobs-report-options.png
   :scale: 60%

--------
Job logs
--------

The Job logs page display 

   * logs for all kind of jobs (backup, restore, copy, etc.) available from Job reports page
   * show time and logs informations (usefull for troubleshooting backup problems)

.. image:: /_static/bacula-web-job-logs.jpg
   :scale: 20%

------------------------
Pools and volumes report
------------------------

List all pools and volumes and display useful volumes information like volume name, bytes, media type, expiration date, last written date, status

.. image:: /_static/bacula-web-pools-volumes.jpg
   :scale: 20%
                                                                                                                                                                                 
------------------
Backup jobs report
------------------

Display usefull information like last 7 days stored bytes and files

   * last completed jobs
   * last 7 days stored bytes and files graphs

.. image:: /_static/bacula-web-backupjob-report.jpg
   :scale: 20%

---------------------
Clients backup report
---------------------

Show information like 
   * Client name
   * Client os
   * Client architecture
   * Client version
   * Last known completed backup job
   * Last 7 days stored bytes and files graphs

.. image:: /_static/bacula-web-client-report.jpg
   :scale: 20%

---------
Test page
---------

This is the page you'd use after instaling Bacula-Web for the first time or if you need to make sure that your installation will work as epxected.

The test page do the following check for you

   * PHP - gettext support (uses for translation)
   * PHP - session support (used in the Core php code)
   * PHP - GD support (used for graphs)
   * PHP - MySQL support
   * PHP - postgreSQL support
   * PHP - sqlite support
   * PHP - PDO support
   * Smarty cache template permissions (required for page rendering purpose)
   * PHP version (version 5.6 at least is supported)

.. image:: /_static/bacula-web-test-page.png
   :scale: 20%
