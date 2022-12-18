## Bacula-Web

[![Documentation Status](https://readthedocs.org/projects/bacula-web/badge/?version=latest)](http://docs.bacula-web.org/en/master/?badge=latest)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=bacula-web_bacula-web&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=bacula-web_bacula-web)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=bacula-web_bacula-web&metric=bugs)](https://sonarcloud.io/summary/new_code?id=bacula-web_bacula-web)

![Bacula-Web dashboard](https://www.bacula-web.org/bacula-web-dashboard.png)

Bacula-Web is a web based tool written in [PHP](https://php.net) which provides a summarized view of your [Bacula](https://www.bacula.org) backup infrastructure.

All the metrics and information provided by Bacula-Web are taken from [Bacula](https://www.bacula.org) catalog database, so there's no need to set up bconsole, bvfs, etc

In addition, the accesses to the database are made read-only, so your Bacula catalog is not altered by Bacula-Web

---

### Why this project ?

Bacula-Web project has been revived since 2010.

I thought that having an easy and useful web UI to monitor Bacula backup jobs, volumes, pools, etc. would be nice.
So I decided to take care of this project which was almost abandoned since more than 4 years.

For more information, please check the [project history](https://docs.bacula-web.org/en/latest/01_about/about.html#the-project-history)

> This project is just my little contribution to [Bacula](http://www.bacula.org) community project.
> I hope you'll find it useful and enjoy it !

---

### Main features

Bacula-Web provides tons of features such as

- Main dashboard (gives you the overall status of your backups, volumes, etc. at a glance)
- Pools and Volumes reports
- Jobs report
- Directors report
- Job files report
- Translated in more than 15 languages
  Bacula-Web has been translated in [more than 15 languages](https://www.transifex.com/bacula-web/public/) by the community users :heart:
- and even more ...

---

### Documentation

Bacula-Web documentation is available using the link below
[Bacula-Web documentation](http://docs.bacula-web.org)

A huge thanks to [Read The Docs](https://readthedocs.org/) for providing a free documentation hosting :heart:

---

### Getting started

#### Requirements

To run Bacula-Web, you'll need at least PHP 7.3 with following extensions enabled

- Gettext
- Session
- PDO
- MySQL, postgresSQL
- SQLite (required for user authentication)
- CLI
- JSON
- Posix

You'll find more details in the [installation guide](https://docs.bacula-web.org/en/latest/02_install/requirements.html)

#### Installation

The most recommended way to install Bacula-Web is by using [Composer](https://getcomposer.org/download)

- Install required packages and setup one of the following web server of your choice
  - [Nginx](https://docs.bacula-web.org/en/latest/02_install/nginx-installation.html)
  - [Lighttpd](https://docs.bacula-web.org/en/latest/02_install/lighttpd-installation.html)
  - [Apache](https://docs.bacula-web.org/en/latest/02_install/apache-installation.html)
  
- Make sure you have Composer installed on your server
- Run the command below
  ```shell
  $ composer create-project --no-dev bacula-web/bacula-web bacula-web
  $ cd bacula-web
  $ composer check 
  ```
- Update the configuration based on your environment
  ```shell
  $ cp application/config.php.sample application/config.php
  $ vim | nano application/config.php
  ```
- Setup users authentication
  Run the command below and follow the instructions
  ```shell
  $ sudo -u www-data php bwc setupauth
  ```
- Test your setup using the two following options

  ```shell
  $ sudo -u www-data php bwc setupauth
  ```
  or use the test page https://bacula-web-url/index.php?page=test

#### Alternative way (using the source archive)

Bacula-Web could also be installed [from the source archive](https://docs.bacula-web.org/en/latest/02_install/installarchive.html) which contains
all Composer dependencies pre-installed in it.

**Important note:**

> The only purpose of this pre-installed archive was to provide an easy way for users who weren't able to install Composer on their servers
> 
> Please note that it will not be available once proper packages will be available

More details about the installation or upgrade and configuration can be found in [the documentation](https://docs.bacula-web.org/en/latest/02_install/index.html)

---

### How to get help ?

The best way to get help or ask a question is to submit a bug report using [GitHub project issues](https://github.com/bacula-web/bacula-web/issues).

> Before submitting any issues, please have a look at the [Bugs and feature request guide](https://docs.bacula-web.org/en/latest/03_gethelp/support.html)

For general questions or feedbacks, you can use [GitHub discussions](https://github.com/bacula-web/bacula-web/discussions)

### Contribution

#### Translations

If you want to help translating Bacula-Web in your language, please check the [Contribute to translation](http://docs.bacula-web.org/en/latest/04_contribute/translations.html) page

#### Development

You can contribute by submitting a GitHub pull request, please check the [contribution guide](http://docs.bacula-web.org/en/latest/04_contribute/development.html) for more details.

---

### License

Bacula-Web source code, web site and documentation are provided under [GPLv2](https://github.com/bacula-web/bacula-web/blob/master/LICENSE) license

---

### Credits

- Original author: Juan Luis Francés Jimenez
- Current maintainer: Davide Franco ([@dfranco](https://github.com/dfranco))
- Contributors: [![GitHub contributors](https://img.shields.io/github/contributors/Naereen/badges.svg)](https://github.com/bacula-web/bacula-web/graphs/contributors)

---

### Support the project

Bacula-Web is a free (like a bird) and open source project maintained on spare time, with the great help from the community.

If you enjoy using Bacula-Web and would like to encourage the project efforts, please consider making a small donation using the buttons below.

<a href="https://www.buymeacoffee.com/baculaweb"><img src="https://img.buymeacoffee.com/button-api/?text=Buy me a coffee&emoji=&slug=baculaweb&button_colour=FFDD00&font_colour=000000&font_family=Lato&outline_colour=000000&coffee_colour=ffffff"></a>

Thanks for using and supporting Bacula-Web project :heart:
