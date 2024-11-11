# FAQ

This page contain the answer to the most frequent questions from community users organised by topics.

## General topics

### What is Bacula-Web ?

Bacula-Web goal is to provide a reporting and monitoring solution for your Bacula backup infrastructure.
It provides useful information about Bacula Jobs, Pools, Volumes, clients, etc.

### How much does Bacula-Web cost ? Is it free of charge ?

**Do I need to pay anything to use it ?**

Nope, I'm glad to say that Bacula-Web, as an open source project, can be downloaded and used without any costs, and it always will be.

**What about the license ?**

Unless it's clearly written, Bacula-Web project source code, documentations, logo, website, etc. are released under the terms of GPLv2 (for more details, see [LICENSE](https://github.com/bacula-web/bacula-web/blob/master/LICENSE)

### Does Bacula-Web supports any web browser ?

As far as I know, Bacula-Web there is no known compatibility issue with any recent web browser.

Bacula-Web works without problem using latest version of Chrome, Firefox, Brave (used during development process).

If you would like to share some feedback using a specific one, don't hesitate to share your experience with any other web browser by giving to [a feedback](https://github.com/bacula-web/bacula-web/discussions)

The only thing you’ve to worry about is to make sure that Javascript is not disabled.

### Which version of Bacula are supported ?

You can use Bacula-Web with any version of Bacula.

::::tip
You may face problems while using really old or latest versions of Bacula.
If you face any problem, feel free to submit a bug report and I'll do my best to help you or make a bug fix.
::::

### Which Bacula catalog database engine are supported by Bacula-Web ?

As of current version of Bacula-Web (version 9.4.0), Bacula catalog running with MySQL, MariaDB and postgreSQL are supported.

Bacula catalog running with MySQL, MariaDB, postgreSQL and SQLite databases are supported.

::::warning
Support for SQLite Bacula catalog was removed on version v9.4.0, but as Bacula community edition still support it,
I decided to continue supporting it in Bacula-Web project since v9.6.0.
::::

### On which OS can I install Bacula-Web ?

Bacula-Web is currently developed and tested under Centos 6 and Red Hat EL version 5.

But it should work fine on your preferred Linux distributions as

* Debian / Ubuntu (any deb based Linux OS)
* Fedora / Centos / Rocky Linux / AlmaLinux / Oracle Linux (any rpm based Linux OS)
* Gentoo
* Slackware
* OpenSuse

I've never tried running Bacula-Web on MS Windows using XAMPP or WampServer, but I don't see anything blocking
users to do it.

In case you need further help, don't hesitate to get back to me by mail (bacula-dev at dflc dot ch)

### What's the current status of Bacula-Web ?

As described in the [About](../about/index.md) page, I revived the Bacula-Web project since end of 2010 after few years without bug fixes and improvements,
and a lot of effort has been made a provide more stable, secure and useful tool, with the precious help from the [community](https://github.com/bacula-web/bacula-web/graphs/contributors).

**So what is the current status ?**

Despite the fact that I think there's still a lot to potential improvements, latest released version is stable and bug free.

### Why reviving Bacula-Web project ?

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

## Installation

### What are the requirements to use Bacula-Web on my server ?

A full [list of requirements](../install/requirements.md) is documented in the documentation section.

### Which version of PHP is supported ?

As of Bacula-Web version 9.0.0, the required PHP version is 8.0 or later

::::warning
PHP versions prior to 8.0 are EOL, theses versions no longer have security support and are exposed to non patched security vulnerabilities.

For more details, please have a look at the [supported PHP version](http://php.net/supported-versions.php) (PHP.net website)
::::

### Where can I download latest version of Bacula-Web ?

Even I would love too, unfortunately, there's no rpm or deb binary package available (yet).

I used to publish a "package" provided which were available in each releases [GitHub release notes](https://github.com/bacula-web/bacula-web/releases)

But as it was causing more issues than really helping users, I decided to stop publishing this "package" (which consists only of pre-installed Composer dependencies).

But, I may think again about this for a future major version.

## Support

### How can I submit a bug and features report ?

Bugs and feature requests are tracked using `GitHub issues <https://github.com/bacula-web/bacula-web/issues>`_.

::::tip
You can find more information on how to submit a bug report [here](support.md)
::::

## Troubleshooting

### After installing Bacula-Web, I only get a blank page, what could be wrong ?

First, ensure that running the test page, everything is ok (use the example link below)

> http://yourserver/bacula-web/test

Make sure Composer dependencies are correctly installed by running this command from the root of Bacula-Web installation folder

```shell
$ composer check
```

*The output should not contain any errors/warnings from Composer*

Also, make sure you ran Bacula-Web console check tool

```shell
$ sudo -u www-data php bwc check
```

*The output should not contain any error / warning*

If above instructions didn't help, then you can get some help by creating an issue on the [GitHub project](https://github.com/bacula-web/bacula-web/issues)

### Why I can't connect to remote db server with SELinux enforced ?

If you gave right permissions and access to your database user, I guess that SELinux is the problem

Check your log file (`/var/log/audit/audit.log` on RedHat/Centos) for the error below

```shell
type=AVC msg=audit(1346832664.222:2491): avc:  denied  { name_connect } for  pid=3427 comm="httpd" dest=3306 scontext=unconfined_u:system_r:httpd_t:s0 tcontext=system_u:object_r:mysqld_port_t:s0 tclass=tcp_socket
type=SYSCALL msg=audit(1346832664.222:2491): arch=40000003 syscall=102 success=no exit=-13 a0=3 a1=bfb94dd0 a2=b63d80c0 a3=c items=0 ppid=3421 pid=3427 auid=0 uid=48 gid=48 euid=48 suid=48 fsuid=48 egid=48 sgid=48 fsgid=48 tty=(none) ses=32 comm="httpd" exe="/usr/sbin/httpd" subj=unconfined_u:system_r:httpd_t:s0 key=(null)
```

and disable SELinux on your server

```shell
$ sudo setenforce permissive
```

or

```shell
$ sudo setenforce disabled
```

::::tip
See [this section](faq.md#how-to-setup-bacula-web-with-selinux-enforced) for further information to run Bacula-Web with
SELinux in enforced mode.
::::

### How to setup Bacula-Web with SELinux enforced

Short answer, Yes

If you are facing issues while using SELinux in enforced mode, make sure bacula-web files and folders must have the correct SELinux context.

Assuming you have installed the files in this directory

`/var/www/html/bacula-web`

you can fix the SELinux context by running the command below

```shell
$ sudo chcon -t httpd_sys_content_t /var/www/html/bacula-web/ -R
```

Otherwise, the simplest would be to set SELinux to Permissive or Disabled
