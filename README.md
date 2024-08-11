# Bacula-Web

[![Latest Stable Version](http://poser.pugx.org/bacula-web/bacula-web/v)](https://packagist.org/packages/bacula-web/bacula-web) [![Total Downloads](http://poser.pugx.org/bacula-web/bacula-web/downloads)](https://packagist.org/packages/bacula-web/bacula-web) [![Latest Unstable Version](http://poser.pugx.org/bacula-web/bacula-web/v/unstable)](https://packagist.org/packages/bacula-web/bacula-web) [![License](http://poser.pugx.org/bacula-web/bacula-web/license)](https://packagist.org/packages/bacula-web/bacula-web) [![PHP Version Require](http://poser.pugx.org/bacula-web/bacula-web/require/php)](https://packagist.org/packages/bacula-web/bacula-web) [![Documentation Status](https://readthedocs.org/projects/bacula-web/badge/?version=latest)](http://docs.bacula-web.org/en/master/?badge=latest) [![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=bacula-web_bacula-web&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=bacula-web_bacula-web) [![Bugs](https://sonarcloud.io/api/project_badges/measure?project=bacula-web_bacula-web&metric=bugs)](https://sonarcloud.io/summary/new_code?id=bacula-web_bacula-web)[![Build Status](https://app.travis-ci.com/bacula-web/bacula-web.svg?branch=master)](https://app.travis-ci.com/bacula-web/bacula-web)

![Bacula-Web dashboard](https://www.bacula-web.org/bacula-web-dashboard.png)

> [!IMPORTANT]
> The master branch is used for development purpose only, don't use it to run Bacula-Web in a production environment.
> You should use [latest stable release](https://github.com/bacula-web/bacula-web/releases/tag/v9.5.1) which is stable and bug free.

Bacula-Web is an open source reporting and monitoring tool for [Bacula](https://www.bacula.org).

It provides a lot of reports and information about [Bacula](https://www.bacula.org) backup infrastructure.

All the metrics and information provided by Bacula-Web are taken from [Bacula](https://www.bacula.org) catalog database
and only require read-only access to the Bacula director catalog database.

## Main features

- Main dashboard (gives you an overall status of your backups jobs, used volumes, weekly backup statistics, etc.)
- Jobs report, Jobs logs, Pools and Volumes reports
- Several Bacula directors statistics from a single web UI
- Responsive design using [Bootstrap](https://getbootstrap.com/)
- Bacula Directors report which gives you an overview of each Bacula director(s) statistics
- Browse backup jobs files and folders
- Users authentication
- Translated in more than 15 languages with the help from community users :heart: [project on Lokalise](https://app.lokalise.com/public/95070757669f26e4c3f8e9.76656729/).

Please see the full [the documentation](https://docs.bacula-web.org/en/latest/01_about/features.html) for the full list of features. 

## Documentation

You can find the complete documentation of Bacula-Web at [https://docs.bacula-web.org](https://docs.bacula-web.org)

> Huge thanks to [Read The Docs](https://readthedocs.org/) for supporting open source projects documentation :heart:

## How to install

Bacula-Web can be installed using

- [Composer package](https://packagist.org/packages/bacula-web/bacula-web)
- [Docker](docker/README.md)
- From source (for advanced users)

### Requirements

- [PHP](https://www.php.net/) >= 8.1 (*7.4 supported, but using EOL versions is not recommended*)
- PHP extensions
  - [Ctype](https://www.php.net/book.ctype)
  - [iconv](https://www.php.net/book.iconv)
  - [JSON](https://www.php.net/book.json)
  - [PCRE](https://www.php.net/book.pcre)
  - [Session](https://www.php.net/book.session)
  - [SimpleXML](https://www.php.net/book.simplexml)
  - [Sqlite](https://www.php.net/manual/en/book.sqlite3.php) (used for local users' authentication)
  - [PDO](https://www.php.net/manual/en/book.pdo.php)
  - [MySQL](https://www.php.net/manual/en/set.mysqlinfo.php)
  - [postgreSQL](https://www.php.net/manual/en/book.pgsql.php)
  - [Tokenizer](https://www.php.net/book.tokenizer)
- [npm](https://nodejs.org/en/learn/getting-started/an-introduction-to-the-npm-package-manager)
- [Composer](https://getcomposer.org/doc/00-intro.md) installed

### Getting started

Use composer cli
```shell
$ composer create-project --no-dev bacula-web/bacula-web bacula-web
```

Install Javascript and CSS dependencies
```shell
$ npm install && npm run build
```

Update `.env` with your configuration 

Ensure `var` folder is writable by the web server process user (see section in [documentation](https://docs.bacula-web.org/en/latest/02_install/installcomposer.html#fix-files-folders-ownership-and-permissions))

Setup users authentication database
```shell
$ php bin/console doctrine:migrations:migration
```

[Set up the webserver](https://docs.bacula-web.org/en/latest/02_install/webserver-setup.html#web-server-setup-and-configuration) of your choice (Apache, Nginx or Lighttpd)

Create your first user

```shell
$ sudo -u www-data php bwc user-create <username>
```

Test your setup using the console

```shell
$ sudo -u www-data php bwc check
```

or use the test page https://bacula-web-url/test

## How to get help

To report an issue or request a new feature, use [GitHub project issues](https://github.com/bacula-web/bacula-web/issues).

> Please see the [bugs and feature request guide](https://docs.bacula-web.org/en/latest/03_get-help/support.html) before.

For questions or feedbacks, please use [GitHub discussions](https://github.com/bacula-web/bacula-web/discussions) or 
contact me at [hello@bacula-web.org](mailto:hello@bacula-web.org).

## How to contribute

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

### Contributors

<a href="https://github.com/bacula-web/bacula-web/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=bacula-web/bacula-web" alt="Bacula-Web dashboard" />
</a>

## License

GPLv2 or later

See the [license file](LICENSE) for further details.

## Security

You've found a security issue ? Good catch!

See [SECURITY.md](SECURITY.md) for further information.

## Credits

- Original author: Juan Luis Francés Jimenez
- Current maintainer: [Davide Franco](https://github.com/dfranco)
  and [community contributors](https://github.com/bacula-web/bacula-web/graphs/contributors)

## Sponsors

<table>
  <tr>
    <td><a href="https://jb.gg/OpenSourceSupport"><img width="180px" src="https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg" alt="JetBrains Logo (Main) logo"></a></td>
    <td><a href="https://www.travis-ci.com"><img width="180px" src="https://www.travis-ci.com/wp-content/uploads/2022/05/TravisCI-Full-Color.png" alt="Travis CI logo"></a></td>
    <td><a href="https://packagecloud.io/"><img width="180px" alt="Private NPM repository and Maven, RPM, DEB, PyPi and RubyGems Repository · packagecloud" src="https://packagecloud.io/images/packagecloud-badge.png" /></a></td>
    <td><a href="https://lokalise.com/"><img width="180px" alt="Lokalise logo" src="https://www.bacula-web.org/sponsors/lokalise_logo_colour_black_text.png" /></a></td>
  </tr>
</table>

## Support the project

Bacula-Web is an open source project and will always be free of charge.

It is maintained on my spare time, with the great help from the community users.

If you enjoy using Bacula-Web and would like to encourage the project efforts, please consider suporting the project by making a small donation
using the buttons below.

<a href="https://www.buymeacoffee.com/baculaweb"><img src="https://img.buymeacoffee.com/button-api/?text=Support the project&emoji=&slug=baculaweb&button_colour=FFDD00&font_colour=000000&font_family=Inter&outline_colour=000000&coffee_colour=ffffff" /></a>

Thanks for using and supporting Bacula-Web project :heart:
