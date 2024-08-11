.. _install/requirements:

************
Requirements
************

General requirements
====================

+-----------------+--------------------------------------------+
| Requirement     | Note                                       |
+=================+============================================+
| **Bacula**                                                   |
+-----------------+--------------------------------------------+
|                 | Community version >= 5.2.9                 |
+-----------------+--------------------------------------------+
| **Web servers**                                              |
+-----------------+--------------------------------------------+
|                 | Apache >= 2.4 (mod_rewrite enabled)        |
+-----------------+--------------------------------------------+
|                 | Nginx >= 1.24                              |
+-----------------+--------------------------------------------+
|                 | Lighttpd >= 1.4.*                          |
+-----------------+--------------------------------------------+
| **PHP version**                                              |
+-----------------+--------------------------------------------+
|                 | PHP >= 7.4 (up to >= 8.3)                  |
+-----------------+--------------------------------------------+
| **PHP extensions**                                           |
+-----------------+--------------------------------------------+
|                 | - Gettext                                  |
|                 | - Session                                  |
|                 | - PDO                                      |
|                 | - MySQL, postgreSQL                        |
|                 | - SQLite (required for user authentication)|
|                 | - CLI                                      |
|                 | - JSON                                     |
+-----------------+--------------------------------------------+

.. important::

   PHP SQLite is required since version 8.0.0-rc2

   PHP Posix used to be required since version 8.0.0, but this requirements has been remove since version 10.0

Using SELinux
=============

To install Bacula-Web with SELinux enforced, please use instructions below

*Should you use SELinux on your server ?*

My answer is **YES**, For security purpose, I would strongly encourage people to keep SELinux enabled.

Check if SELinux is enabled

As root, run the command below to check how SELinux is configured on your system

::

   # getenforce

If you get a status

::

   Enforced

and Bacula-Web doesn't work, first of all, check Selinux log file

::

   # tail /var/log/audit/audit.log

If you see entries related to Bacula-Web script files ....

::

   ...
   type=AVC msg=audit(1418826191.935:69): avc:  denied  { relabelto } for  pid=1595 comm="chcon" name="%%F7^F7F^F7F34188%%header.tpl.php" dev=dm-0 ino=403104 scontext=unconfined_u:unconfined_r:unconfined_t:s0-s0:c0.c1023 tcontext=system_u:object_r:removable_device_t:s0 tclass=file
   type=SYSCALL msg=audit(1418826191.935:69): arch=40000003 syscall=227 success=no exit=-13 a0=bfb2700c a1=383629 a2=99b1bd8 a3=28 items=0 ppid=1319 pid=1595 auid=0 uid=0 gid=0 euid=0 suid=0 fsuid=0 egid=0 sgid=0 fsgid=0 tty=pts0 ses=1 comm="chcon" exe="/usr/bin/chcon" subj=unconfined_u:unconfined_r:unconfined_t:s0-s0:c0.c1023 key=(null)
   ....

Restore SELinux security context
--------------------------------

This command should fix the problem

::

   # restorecon -R -v /var/www/html/bacula-web

For users using Centos/Red Hat version 7, use the command below to set the right security context on the cache folder

::

   # chcon -R -t httpd_sys_rw_content_t /var/www/html/bacula-web/application/view/cache

If you are running Apache web server, make sure the PHP session path (see path below) have the correct SELinux context.

::

    /var/lib/php/session

To check it, run this command on your server

::
  
    php -i | grep session.save_path

and if needed, restore the correct SELinux context

::

    # restorecon -Rv /var/lib/php/session

Resources
----------

If you need more information about SELinux and security, use the links below

   * `Centos wiki`_
   * `Fedora project wiki`_
   * `Red Hat - Working with SELinux`_

.. _Red Hat - Working with SELinux: https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_Linux/6/html/Security-Enhanced_Linux/chap-Security-Enhanced_Linux-Working_with_SELinux.html
.. _Fedora project wiki: http://fedoraproject.org/wiki/SELinux
.. _Centos wiki: http://wiki.centos.org/HowTos/SELinux
