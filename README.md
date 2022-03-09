## Bacula-Web

[![Documentation Status](https://readthedocs.org/projects/bacula-web/badge/?version=latest)](http://docs.bacula-web.org/en/master/?badge=latest)
[![Maintainability](https://api.codeclimate.com/v1/badges/db37b493624cee584d85/maintainability)](https://codeclimate.com/github/bacula-web/bacula-web/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bacula-web/bacula-web/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/bacula-web/bacula-web/?branch=develop)

Bacula-Web is a web based tool written in [PHP](https://php.net) which provides a summarized view of your [Bacula](https://www.bacula.org) backup infrastructure.

Metrics and informations displayed in Bacula-Web are taken from [Bacula](https://www.bacula.org) catalog database, no need to setup bconsole, bvfs, etc.

In addition, the access to the database is made read-only, so your Bacula catalog database remains safe :)

---

### Why this project ?

Bacula-Web project has been revived since 2010.

I tought that having an easy and useful web UI to monitor Bacula backup jobs, volumes, pools, etc. would be nice.
So I decided to take care of this project which was almost abandonned since more than 4 years.

You can find more information about the project timeline in the official documentation [here](https://docs.bacula-web.org/en/latest/01_about/about.html#the-project-history)

*This is my little contribution to [Bacula](http://www.bacula.org) community project. Hope you enjoy it !*

<p align="center">
<img src="https://www.bacula-web.org/bacula-web-dashboard.png" width="500px" alt="Bacula-Web dashboard"/>
</p>

---

### Features

* **Easy to setup**
  > It takes only few minutes to setup Bacula-Web, read the [documentation](https://docs.bacula-web.org/en/latest/02_install/index.html)
* **Install once**
  > Install Bacula-Web once, then monitor as many Bacula directors you have in your infrastructure
* **Secure**
  > Users authentication is enabled by default, no Bacula's information are disclosed
* **Dashboard**
  > Bacula-Web dashboard gives you a general overview of your [Bacula](https://www.bacula.org) backup jobs, pools, volumes, etc.
* **Use it in your native language**
  > Bacula-Web has been translated in [more than 15 languages](https://www.transifex.com/bacula-web/public/) by the community users ❤️
* **Jobs report**
  > Monitor backup jobs from a single one page (filter and options are also available)
* **Job Files report**
  > List files and folders from backup job(s)
* **Directors report**
  > High-level report pages for all configured [Bacula](https://www.bacula.org) directors
* **Pools and volumes**
  > Display all your [Bacula](https://www.bacula.org) pools and volumes

---

### Documentation

Have a look at [Bacula-Web documentation](http://docs.bacula-web.org) hosted by [Read The Docs](https://readthedocs.org/) to get more informations about installation, configuration, upgrade, etc.

---

### Installation

Bacula-Web can be installed [from the source archive](https://docs.bacula-web.org/en/latest/02_install/installarchive.html), but the easiest and recommanded way to install it is by using [Composer](https://getcomposer.org/).

To proceeed, make sure you have Composer installed using [these instructions](https://getcomposer.org/download/)

Then run the command below to install Bacula-Web

```
$ composer create-project --no-dev bacula-web/bacula-web bacula-web
```

Otherwise, Bacula-Web can be installed using the provided compressed archive or using Composer, check [Installation page](http://docs.bacula-web.org/en/latest/02_install/index.html) for more details

Latest stable release compressed archive can be found in [latest GitHub release](https://github.com/bacula-web/bacula-web/releases)

---

### How to get help ?

Best way to get help or ask a question is to submit a bug report using [GitHub project issues](https://github.com/bacula-web/bacula-web/issues)

### How to contribute ?

#### Translations

If you want to help translating Bacula-Web in your language, please check the [Contribute to translation](http://docs.bacula-web.org/en/latest/04_contribute/translations.html) page

#### Developpment

You can contribute by submitting a GitHub pull request, please have a look the [contribution guide](http://docs.bacula-web.org/en/latest/04_contribute/development.html) first.

---

### License

Bacula-Web source code, web site and documentation are licensed under **GNU GPLv2**

For more details, see [LICENSE](https://github.com/bacula-web/bacula-web/blob/master/LICENSE)

---

### Credits

- Original author: Juan Luis Francés Jimenez
- Current maintainer: Davide Franco ([@dfranco](https://github.com/dfranco))
- Contributors: [![GitHub contributors](https://img.shields.io/github/contributors/Naereen/badges.svg)](https://GitHub.com/bacula-web/bacula-web/badges/graphs/contributors/)

---

### Support the project

As you probably already know, developpers does needs a lot of coffee :coffee: to be more efficient :wink:, so if you think Bacula-Web project desearve a little help, please consider buying me a coffee using the link below.

<a href="https://www.buymeacoffee.com/baculaweb"><img src="https://img.buymeacoffee.com/button-api/?text=Buy me a coffee&emoji=&slug=baculaweb&button_colour=FFDD00&font_colour=000000&font_family=Lato&outline_colour=000000&coffee_colour=ffffff"></a>

Thanks for using and supporting Bacula-Web project :heart:
