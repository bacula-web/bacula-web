# Bacula-Web

[![Packagist Version](https://img.shields.io/packagist/v/bacula-web/bacula-web)](https://packagist.org/packages/bacula-web/bacula-web)
[![Packagist Downloads](https://img.shields.io/packagist/dt/bacula-web/bacula-web)](https://packagist.org/packages/bacula-web/bacula-web)
[![License](https://img.shields.io/packagist/l/bacula-web/bacula-web)](https://packagist.org/packages/bacula-web/bacula-web)
[![Required PHP version](https://img.shields.io/packagist/dependency-v/bacula-web/bacula-web/php)
[![Documentation Status](https://readthedocs.org/projects/bacula-web/badge/?version=latest)](http://docs.bacula-web.org/en/master/?badge=latest)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=bacula-web_bacula-web&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=bacula-web_bacula-web)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=bacula-web_bacula-web&metric=bugs)](https://sonarcloud.io/summary/new_code?id=bacula-web_bacula-web)
[![Build Status](https://app.travis-ci.com/bacula-web/bacula-web.svg?branch=master)](https://app.travis-ci.com/bacula-web/bacula-web)

![Bacula-Web dashboard](https://www.bacula-web.org/bacula-web-dashboard.png)

Bacula-Web is an open source reporting and monitoring tool for [Bacula](https://www.bacula.org).

It provides a lot of reports and informations about [Bacula](https://www.bacula.org) backup infrastructure.

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
- Translated in more than 15 languages with the help from community users :heart: [project on Transifex](https://explore.transifex.com/bacula-web/bacula-web/).

Please see the full [features list](https://www.bacula-web.org/docs/about/features) from the documentation. 

## Documentation

Please read the documentation of Bacula-Web at [https://www.bacula-web.org/docs](https://www.bacula-web.org/docs)

> Previous documentation is still available at [https://docs.bacula-web.org](https://docs.bacula-web.org)
>
> Huge thanks to [Read The Docs](https://readthedocs.org/) for hosting open source projects documentation over the past few years :heart:

## Getting started

Bacula-Web can be installed using

- the pre-built archive (available in [releases](https://github.com/bacula-web/bacula-web/releases) on GitHub)
- [Docker](docker/README.md)
- [git source](https://github.com/bacula-web/bacula-web) using [Composer](https://getcomposer.org/) (require advanced skills)

Please read the [Getting started](https://www.bacula-web.org/docs/install/getting-started) page for further installation instructions.

> **Note about the pre-built archive**
>
> The pre-built archive contains pre-installed dependencies (no need to use Composer anymore) and is available since version 9.8.0

## Getting help

To report an issue or request a new feature, use [GitHub project issues](https://github.com/bacula-web/bacula-web/issues).

> Please see the [bugs and feature request guide](https://www.bacula-web.org/docs/gethelp/support) before.

For questions or feedbacks, please use [GitHub discussions](https://github.com/bacula-web/bacula-web/discussions) or 
contact me at [hello@bacula-web.org](mailto:hello@bacula-web.org).

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

### Contributors

<a href="https://github.com/bacula-web/bacula-web/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=bacula-web/bacula-web" />
</a>

## License

The GPLv2. Please see [license file](LICENSE) for more information.

## Security

If you discover a security issue, see [SECURITY.md](SECURITY.md)

## Credits

- Original author: Juan Luis Francés Jimenez
- Current maintainer: [Davide Franco](https://github.com/dfranco)
  and [community contributors](https://github.com/bacula-web/bacula-web/graphs/contributors)

## Sponsors

<table>
<tr>
<td><a href="https://www.docker.com/"><img src="https://www.bacula-web.org/assets/images/docker-logo-0f1d943e8a1505d609e538df99c8eee0.png" alt="Docker Logo" width="140px"></a></td>
<td><a href="https://jb.gg/OpenSourceSupport"><img src="https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg" alt="JetBrains Logo (Main) logo"></a></td>
<td><a href="https://www.travis-ci.com"><img src="https://www.travis-ci.com/wp-content/uploads/2024/07/cropped-travis-ci-mascot-1-480x480-1.png" width="140px" alt="Travis CI logo"></a></td>
<td><a href="https://packagecloud.io/"><img alt="Private NPM repository and Maven, RPM, DEB, PyPi and RubyGems Repository · packagecloud" src="https://packagecloud.io/images/packagecloud-badge.png" /></a></td>
<td><a href="https://lokalise.com/"><img alt="Lokalise logo" src="https://lokalise.com/img/lokalise_logo_black.png" width="240px" /></a></td>
</tr>
</table>

## Support the project

Bacula-Web is an open source project and will always be free of charge.

It is maintained on my spare time, with the great help from the community users.

If you enjoy using Bacula-Web and would like to encourage the project efforts, please consider suporting the project by making a small donation
using the buttons below.

<a href="https://www.buymeacoffee.com/baculaweb"><img src="https://img.buymeacoffee.com/button-api/?text=Support the project&emoji=&slug=baculaweb&button_colour=FFDD00&font_colour=000000&font_family=Inter&outline_colour=000000&coffee_colour=ffffff" /></a>

Thanks for using and supporting Bacula-Web project :heart:
