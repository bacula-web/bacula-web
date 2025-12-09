---
slug: /faq
---

# FAQ

This page contains the answer to the most frequent questions from community users, organized by topics.

## General topics

### What is Bacula-Web?

Bacula-Web's goal is to provide a reporting and monitoring solution for your Bacula backup infrastructure.
It provides useful information about [Bacula](https://www.bacula.org/) Jobs, Pools, Volumes, clients, etc.

### Is Bacula-Web free of charge?

Yes, I'm glad to say that Bacula-Web source code and documentation are open source and free of charge, forever.

### What about the license?

Unless it's clearly mentioned, Bacula-Web project source code, documentations, logo, website, etc., are released under the terms of Gnu Public License version 2 (GPLv2)

See the [LICENSE](https://github.com/bacula-web/bacula-web/blob/9.x/LICENSE) for all details.

### Which web browsers are supported?

As far as I know, Bacula-Web, there is no known compatibility issue with any recent web browser.

Bacula-Web works without problem using the latest version of Chrome, Firefox, and Brave (used during the development process).

If you would like to share some feedback using a specific one, don't hesitate to share your experience with any other web browser by giving [feedback](https://github.com/bacula-web/bacula-web/discussions)

Make sure that your web browser is not blocking Javascript and allow cookies.

### Which version of Bacula can I use?

You can use Bacula-Web with any version of [Bacula Community Edition](https://www.bacula.org/)

::::tip
You may face problems while using really old or the latest versions of Bacula.
If you face any problem, feel free to submit a bug report, and I'll do my best to help you or make a bug fix.
::::

### Which Bacula catalog database engines are supported using Bacula-Web ?

As of the current version of Bacula-Web (version 9.9.1), Bacula catalogs running on

* MySQL / MariaDB
* SQLite
* PostgreSQL

::::info
Support for SQLite Bacula catalog was removed on version v9.4.0, but as Bacula community edition still support it,
So, I decided to continue supporting it in the Bacula-Web project since v9.6.0.
::::

### Can I install Bacula-Web on any Os?

Bacula-Web is currently developed and tested on Ubuntu 24.04 and RockyLinux 9.

It is also known to work without issues on the following operating systems.

* Debian / Ubuntu (any Debian-based Linux OS)
* Fedora / Centos / Rocky Linux / AlmaLinux / Oracle Linux (any rpm based Linux OS)
* Gentoo
* Slackware
* OpenSuse
* FreeBSD

#### Support for MS Windows

I've never tried running Bacula-Web on MS Windows using XAMPP or WampServer, but I don't see anything blocking users from doing it.

In case you need further help, don't hesitate to get back to me by e-mail at hello@bacula-web.org

### Is Bacula-Web stable enough to be used in production?

As described in the [About](/docs/about) page, I revived the Bacula-Web project at the end of 2010 after a few years without bug fixes and improvements, and a lot of effort has been made to provide a more stable, secure, and useful tool, with the precious help from the [community](https://github.com/bacula-web/bacula-web/graphs/contributors).

Despite the fact that I think there's still a lot to potential improvements, the latest released version is stable and bug free.

There are always something to improve, but, unless there's a known issues or limitation,
you can consider [latest release](https://github.com/bacula-web/bacula-web/releases) stable and safe to be used in your environment.

### What can I expect from next releases?

You can expect Bacula-Web to be ...

* Easier to install, configure and upgrade
* Simple and easy to use (you only need a web browser, no heavy GUI to install and configure, or command to run in a terminal)
* Useful (many features are coming, see the [roadmap](https://github.com/bacula-web/bacula-web/milestones))

### What about Bacula-Web project ?

You can have a look at the [About the project](./about/index.md) page.

## Installation

### What are the requirements to use Bacula-Web on my server?

A full [list of requirements](/docs/install/requirements) is documented in the documentation section.

### Which version of PHP is supported?

As of Bacula-Web version 9.0.0, the required PHP version is 8.0 or above.

::::warning
PHP versions prior to 8.0 are EOL; those versions no longer receive security and bug fixes. Using old versions of PHP may expose your server to security vulnerabilities.

For more details, please have a look at the [supported PHP version](http://php.net/supported-versions.php) (PHP.net website)
::::

### Where can I download the latest version of Bacula-Web?

The Bacula-Web source code is available on [GitHub](https://github.com/bacula-web/bacula-web)

Since version 9.8.0, a pre-built package is available from [each releases on GitHub](https://github.com/bacula-web/bacula-web/releases).

### Is there any rpm or deb packages available?

Even I would love to, unfortunately, there's no rpm or deb binary package available (yet).

But my 2026’s objectives is to provides at least some rpm and/or deb packages.

## Support

### How can I report an issue or ask for a new feature ?

Bugs and feature requests are tracked using [GitHub issues](https://github.com/bacula-web/bacula-web/issues).

::::tip
You can find more information on how to submit a bug report [here](./contribute/reporting-issue-guideline.md).
::::

## Troubleshooting

### After installing Bacula-Web, I only get a blank page. What could be wrong?

First, ensure that when running the test page, everything is ok (use the example link below)

> http://yourserver/bacula-web/test

Make sure Composer dependencies are correctly installed by running this command from the root of the Bacula-Web installation directory.

```shell
$ composer check
```

*The output should not contain any errors/warnings from Composer*

Also, make sure you run the Bacula-Web console check tool.

```shell
$ sudo -u www-data php bwc check
```

*The output should not contain any error / warning*

If the above instructions didn't help, then you can get some help by creating an issue on the [GitHub project](https://github.com/bacula-web/bacula-web/issues)

### Why can't I connect to the remote database server?

This might be due to

* Misconfigured Bacula Catalog database connection

  Make sure you've [configured](./install/configure.md) the Bacula Catalog database correctly, and the database user has enough permissions (see [this page](./admin-guides/bacula-database-user.mdx)
* Using a server with SELinux enforced

  For SELinux, you can find more instructions in the [SELinux page](./install/selinux.md).

### Can I install Bacula-Web on a server with SELinux enforced?

Yes, you can install Bacula-Web on a server whith SELinux in enforced mode.

You only need to ensure that the bacula-web files and folders must have the correct SELinux context.

You can find more instructions in the [SELinux page](./install/selinux.md).
















### Why can't I connect to the remote db server with SELinux enforced?

If you have set the [permissions and ownership](./admin-guides/permissions-and-ownership.mdx), and properly configured the Bacula Catalog database connection,

The root of the problem can be

* SELinux in enforced mode
* Bad files/folderes permissions/ownership
* Incorrect setup of the web server
* Missing PHP extensions installed
* ...

