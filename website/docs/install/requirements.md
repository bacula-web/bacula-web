# Requirements

## General requirements

| Supported Bacula version | Version  |
|--------------------------|----------|
| Bacula Community Edition | >= 5.2.9 |

| Supported web servers | Version                           |
|-----------------------|-----------------------------------|
| Apache                | >= 2.4 (with mod_rewrite enabled) |
| Nginx                 | >= 1.24                           |
| Lighttpd              | >= 1.4.*                          |

PHP requirements

| Component | Supported version     |
|-----------|-----------------------|
| PHP       | >= 7.4 (up to >= 8.3) |

| PHP extensions                            |
|-------------------------------------------|
| Gettext                                   |                         
| Session                                   |
| PDO                                       |
| MySQL, postgreSQL                         |
| SQlite (required for user authentication) |
| CLI                                       |
| JSON                                      |

::::info
* PHP SQLite is required since version [8.0.0](https://github.com/bacula-web/bacula-web/releases/tag/v8.0.0)
* PHP Posix used to be required since version 8.0.0, but this requirements has been remove since version 10.0
::::

## SELinux support

For further information on how to setup Bacula-Web on a server with SELinux in enforced mode, see [this page](selinux.md)