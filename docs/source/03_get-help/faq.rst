.. _get-help/faq:

==========================
Frequently asked questions
==========================

Here's below a summary of most frequently asked questions that might help you to install, upgrade, troubleshoot or contribute to Bacula-Web project

General topics
==============

  * :ref:`Which web browsers can be used with Bacula-Web ?<supported-web-browser>`
  * :ref:`Which version of Bacula can be used with Bacula-Web ?<supported-bacula-version>`
  * :ref:`Which Bacula catalog database engine are supported by Bacula-Web ?<supported-db>`
  * :ref:`What's the current status of Bacula-Web ?<current-status>`
  * :ref:`Why reviving Bacula-Web project ?<project-revive>`
  * :ref:`How much does Bacula-Web cost ? Is it free of charge ?<project-license>`

Installation
============

  * :ref:`What do I need to install and run Bacula-Web ? <requirements>`
  * :ref:`Which version of PHP is supported ?<supported-php-version>`
  * :ref:`Where can I download latest version of Bacula-Web ?<download-latest-version>`
  * :ref:`After installing Bacula-Web, I only get a blank page, what could be wrong ?<troubleshoot-blank-page>`
  * :ref:`On which OS can I install Bacula-Web ?<supported-os>`
  * :ref:`Why I cannot connect to remote database server with SELinux enforced ?<troubleshoot-selinux>`
  * :ref:`Does Bacula-Web can run on a system having SELinux enforced ?<troubleshoot-selinux-enforced>`

Support
=======

  * :ref:`How can I submit a bug report or a feature request ?<bug-feature-request>`

General topics
--------------

.. _supported-web-browser:

Which web browsers can be used with Bacula-Web ?
------------------------------------------------

Bacula-Web is compatible with almost all well known web browser. The only thing you’ve to worry about is to make sure that Javascript is enabled.

Before releasing a new version, I usually make some tests with latest version of Firefox, Chrome and Brave.
Don't hesitate to share your experience with any other web browser by giving to me feedback.

.. _supported-bacula-version:

Which version of Bacula can be used with Bacula-Web ?
-----------------------------------------------------

You can use Bacula-Web with any version of Bacula.

But, if you encounter any problems with a specific version of Bacula, then feel free to submit a bug report and I'll do my best to help you or make a bug fix.

.. _supported-db:

Which Bacula catalog database engine are supported by Bacula-Web ?
------------------------------------------------------------------

As of current version of Bacula-Web (version 8.7.0), Bacula catalog running with MySQL, MariaDB, postgreSQL and SQLite databases are supported.

.. _current-status:

What's the current status of Bacula-Web ?
-----------------------------------------

As described in the :ref:`about/about` section, I revived the Bacula-Web project since end of 2010 after few years without bug fixes and improvements.
As you already know, a lot of effort has been made a provide more stable, secure and useful tool.

But there's still a lot of things to do but since version 5.1.0 alpha, Bacula-Web is slightly stable.

For people that use Bacula-Web on a daily basis, you already know that they're a lot improvement to achieve.
That's what I'll try to do on my spare time and hope you'll enjoy

.. _project-revive:

Why reviving Bacula-Web project ?
---------------------------------

Since several years, I'm using as you this amazing open source backup tool Bacula and I was looking for a web based tool that provide me useful information about last night jobs.

My first look were on WeBacula and bweb which are nice to use and features full but maybe pretty much not easy to install and configure.

Then, I've found Bacula-Web which wasn't patched and updated since many years.

I submitted patches to Bacula developer list and after some commit, i proposed to become the official maintainer of this project. That's was on July 2010.

I know that there's a lot of web based console for managing, monitoring and configuring bacula like

  * bweb
  * bat
  * Webacula

You can also find a complete list of GUI in the Bacula's web site

Because bacula-web stand to be (for both next patch and future release)

  * Really easy to install, configure and upgrade
  * Easy to use (after you've successfully installed, you just need a web browser, no GUI)
  * Useful (see roadmap for the next version's coming)

I do use Bacula-Web for personal usage several time per week, and the idea was simply to share those improvements with the community

  * Fixes and enhancement I've created on my side
  * Added features such as a new test page
  * Improved design
  * Fixed some SQL query bugs (MySQL and postgreSQL)

.. _project-license:

How much does Bacula-Web cost ? Is it free of charge ?
------------------------------------------------------

**Bacula-Web is open source**

Bacula-Web source code, documentations, logo, website, etc. are released under the terms of GPLv2 (for more details, see `LICENSE <https://github.com/bacula-web/bacula-web/blob/master/LICENSE>`_)

**Bacula-Web is free**

I'm glad to say that Bacula-Web is open source and free (like a bird).

If you find Bacula-Web useful and would like to encourage the project's efforts, Then I'd be happy to see you part of
the list of bakers. Use `this link <https://www.buymeacoffee.com/baculaweb>`_ if you want to know more about it.

Installation
------------

.. _requirements:

What are the requirements to use Bacula-Web on my server ?
----------------------------------------------------------

A full :ref:`list of requirements <install/requirements>` is documented in the documentation section.

.. _supported-php-version:

Which version of PHP is supported ?
-----------------------------------

As of Bacula-Web version 9.0.0, the required PHP version is version <= 8.0

.. important:: PHP versions prior to 8.0 are not supported anymore, theses versions no longer have security support and are exposed to non patched security vulnerabilities.

For more details, please have a look at the `currently supported PHP version <http://php.net/supported-versions.php>`_ (PHP.net website)

.. _download-latest-version:

Where can I download latest version of Bacula-Web ?
---------------------------------------------------

There's no binary package (rpm, deb) that you can download from anywhere.

The only "package" provided is a pre-installed Composer dependency package which is available in each `GitHub release notes <https://github.com/bacula-web/bacula-web/releases>`_

.. _troubleshoot-blank-page:

After installing Bacula-Web, I only get a blank page, what could be wrong ?
---------------------------------------------------------------------------

First, ensure that running the test page, everything is ok (use the example link below)

::

  http://yourserver/bacula-web/test

Make sure Composer dependencies are correctly installed by running this command from the root of Bacula-Web installation folder

::

  $ composer check

*The output should not contain any errors/warnings from Composer*

Also, make sure you ran Bacula-Web console check tool

::

  $ sudo -u www-data php bwc check

*The output should not contain any error / warning*

If above instructions didn't help, then you can get some help by creating an issue on the `GitHub project <https://github.com/bacula-web/bacula-web/issues>`_

.. _supported-os:

On which OS can I install Bacula-Web ?
--------------------------------------

Bacula-Web is currently developed and tested under Centos 6 and Red Hat EL version 5.

But it should work fine on your preferred Linux distributions as

  * Debian/Ubuntu (or any kind of Debian based distros)
  * Gentoo
  * Slackware
  * OpenSuse
  * Fedora
  * etc.

Bacula-Web should work as well on XAMPP but without any warranty (not tested yet).

If you intend to install Bacula-web on WAMP (Windows + Apache + PHP + MySQL), it should work without problems. 
You just need to ensure that PHP has been compiled with the bacula's database support (MySQL, postgreSQL, SQLite) and PDO as well.

In case you need further help, don't hesitate to get back to me by mail (bacula-dev at dflc dot ch)

.. _troubleshoot-selinux:

Why I can't connect to remote db server with SELinux enforced ?
---------------------------------------------------------------

If you gave right permissions and access to your database user, I guess that SELinux is the problem

Check your log file (/var/log/audit/audit.log on RedHat/Centos) for the error below

::

  type=AVC msg=audit(1346832664.222:2491): avc:  denied  { name_connect } for  pid=3427 comm="httpd" dest=3306 scontext=unconfined_u:system_r:httpd_t:s0 tcontext=system_u:object_r:mysqld_port_t:s0 tclass=tcp_socket
  type=SYSCALL msg=audit(1346832664.222:2491): arch=40000003 syscall=102 success=no exit=-13 a0=3 a1=bfb94dd0 a2=b63d80c0 a3=c items=0 ppid=3421 pid=3427 auid=0 uid=48 gid=48 euid=48 suid=48 fsuid=48 egid=48 sgid=48 fsgid=48 tty=(none) ses=32 comm="httpd" exe="/usr/sbin/httpd" subj=unconfined_u:system_r:httpd_t:s0 key=(null)

and disable SELinux on your server

::

  $ sudo setenforce permissive

or

::

  $ sudo setenforce disabled

.. _troubleshoot-selinux-enforced:

Does Bacula-Web can run on a system having SELinux enforced ?
-------------------------------------------------------------

The short answer is **yes**.

The long answer is below

If nothing seems to be working and you are using SELinux, please remember that you must have the correct contexts for the bacula-web files. Assuming you have installed the files in this directory

:: 

  /var/www/html/bacula-web

you can fix the SELinux context by running the command below

::

  $ sudo chcon -t httpd_sys_content_t /var/www/html/bacula-web/ -R

Otherwise, the simplest would be to set SELinux to Permissive or Disabled

Support
-------

.. _bug-feature-request:

How can I submit a bug and features report ?
--------------------------------------------

Register or log in (if you already registered) in the `bug tracker`_ and submit your bug and/or feature request.

.. _bug tracker: http://bugs.bacula-web.org

More information on how to submit a bug report can be found :ref:`here <get-help/support>`
