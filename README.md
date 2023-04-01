# Bacula-Web

[![Latest Stable Version](http://poser.pugx.org/bacula-web/bacula-web/v)](https://packagist.org/packages/bacula-web/bacula-web) [![Total Downloads](http://poser.pugx.org/bacula-web/bacula-web/downloads)](https://packagist.org/packages/bacula-web/bacula-web) [![Latest Unstable Version](http://poser.pugx.org/bacula-web/bacula-web/v/unstable)](https://packagist.org/packages/bacula-web/bacula-web) [![License](http://poser.pugx.org/bacula-web/bacula-web/license)](https://packagist.org/packages/bacula-web/bacula-web) [![PHP Version Require](http://poser.pugx.org/bacula-web/bacula-web/require/php)](https://packagist.org/packages/bacula-web/bacula-web) [![Documentation Status](https://readthedocs.org/projects/bacula-web/badge/?version=latest)](http://docs.bacula-web.org/en/master/?badge=latest) [![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=bacula-web_bacula-web&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=bacula-web_bacula-web) [![Bugs](https://sonarcloud.io/api/project_badges/measure?project=bacula-web_bacula-web&metric=bugs)](https://sonarcloud.io/summary/new_code?id=bacula-web_bacula-web)[![Build Status](https://app.travis-ci.com/bacula-web/bacula-web.svg?branch=master)](https://app.travis-ci.com/bacula-web/bacula-web)

![Bacula-Web dashboard](https://www.bacula-web.org/bacula-web-dashboard.png)

Bacula-Web is a web based tool written in [PHP](https://php.net) which provides a summarized view of your [Bacula](https://www.bacula.org) backup infrastructure.

All the metrics and information provided by Bacula-Web are taken from [Bacula](https://www.bacula.org) catalog database, so there's no need to set up bconsole, bvfs, etc

In addition, the accesses to the database are made read-only, so your Bacula catalog is not altered by Bacula-Web

## Why this project ?

Bacula-Web project has been revived since 2010.

I thought that having an easy and useful web UI to monitor Bacula backup jobs, volumes, pools, etc. would be nice.
So I decided to take care of this project which was almost abandoned since more than 4 years.

For more information, please check the [project history](https://docs.bacula-web.org/en/latest/01_about/about.html#the-project-history)

> This project is just my little contribution to [Bacula](http://www.bacula.org) community project.
> I hope you'll find it useful and enjoy it !

## Main features

Bacula-Web provides tons of features such as

- Main dashboard (gives you the overall status of your backups, volumes, etc. at a glance)
- Pools and Volumes reports
- Jobs report
- Directors report
- Job files report
- Translated in more than 15 languages
  Bacula-Web has been translated in [more than 15 languages](https://www.transifex.com/bacula-web/public/) by the community users :heart:
- and even more [features](https://docs.bacula-web.org/en/latest/01_about/features.html) ...

## Documentation

More information can be found in the [documentation](https://docs.bacula-web.org)

> A huge thanks to [Read The Docs](https://readthedocs.org/) for supporting OSS projects documentation hosting :heart:

## Getting started

### Requirements

Full [requirements](https://docs.bacula-web.org/en/latest/02_install/requirements.html) list can be found in the official documentation

### Installation

Bacula-Web can be installed using [Composer](https://docs.bacula-web.org/en/latest/02_install/installcomposer.html#install-installcomposer), [Docker](https://hub.docker.com/r/baculaweb/bacula-web) or using the [Composer archive](https://docs.bacula-web.org/en/latest/02_install/installarchive.html#install-installarchive) (will be soon deprecated, see note below)

#### TLDR; (Installation using Composer)

```shell
$ composer create-project --no-dev bacula-web/bacula-web bacula-web
$ cd bacula-web
$ composer check 
```

Update the configuration based on your environment

```shell
$ cp application/config.php.sample application/config.php
$ [vim || nano] application/config.php
```

Setup users authentication by running

```shell
$ sudo -u www-data php bwc setupauth
``` 

Now test your setup by using one of the two options below

```shell
$ sudo -u www-data php bwc check
```

or use the test page https://bacula-web-url/index.php?page=test

> **Important note related to installation using Composer archive**
> 
> The main purpose of this pre-installed archive was to provide an easy way for users who weren't able to install Composer on their servers
> Composer archive installation option will not be supported anymore from next major version (v9.0.0)

## How to get help ?

The best way to get help or ask a question is to submit a bug report using [GitHub project issues](https://github.com/bacula-web/bacula-web/issues).

> Before submitting any issues, please have a look at the [Bugs and feature request guide](https://docs.bacula-web.org/en/latest/03_gethelp/support.html)

For general questions or feedbacks, you can use [GitHub discussions](https://github.com/bacula-web/bacula-web/discussions)

## Contribution

### Translations

If you want to help translating Bacula-Web in your language, please check the [Contribute to translation](http://docs.bacula-web.org/en/latest/04_contribute/translations.html) page

### Development

You can contribute by submitting a GitHub pull request, please check the [contribution guide](http://docs.bacula-web.org/en/latest/04_contribute/development.html) for more details.

## License

Bacula-Web source code, web site and documentation are provided under [GPLv2](https://github.com/bacula-web/bacula-web/blob/master/LICENSE) license

## Credits

- Original author: Juan Luis Francés Jimenez
- Current maintainer: Davide Franco ([@dfranco](https://github.com/dfranco))

## Contributors

<a href="https://github.com/bacula-web/bacula-web/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=bacula-web/bacula-web" />
</a>

*Powered by [contrib.rocks](https://contrib.rocks)*

## Sponsors

<a href="https://jb.gg/OpenSourceSupport"><img src="https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg" alt="JetBrains Logo (Main) logo" height="120"></a> &nbsp;
<a href="https://www.travis-ci.com"><img src="https://www.travis-ci.com/wp-content/uploads/2022/05/TravisCI-Full-Color.png" alt="Travis CI logo" height="80"></a> &nbsp;
<a href="https://packagecloud.io/"><img height="46" width="158" alt="Private NPM repository and Maven, RPM, DEB, PyPi and RubyGems Repository · packagecloud" src="https://packagecloud.io/images/packagecloud-badge.png" /></a>

## Support the project

Bacula-Web is a free (like a bird) and open source project maintained on spare time, with the great help from the community.

If you enjoy using Bacula-Web and would like to encourage the project efforts, please consider making a small donation using the buttons below.

<a href="https://www.buymeacoffee.com/baculaweb"><img src="https://img.buymeacoffee.com/button-api/?text=Support the project&emoji=&slug=baculaweb&button_colour=FFDD00&font_colour=000000&font_family=Inter&outline_colour=000000&coffee_colour=ffffff" /></a>

Thanks for using and supporting Bacula-Web project :heart:
