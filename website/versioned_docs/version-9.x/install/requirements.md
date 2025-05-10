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
| PHP       | >= 8.0 (up to >= 8.4) |

| PHP extensions                            |
|-------------------------------------------|
| Gettext                                   |                         
| Session                                   |
| PDO                                       |
| MySQL, postgreSQL                         |
| SQlite (required for user authentication) |
| CLI                                       |
| JSON                                      |
| Posix                                     | 

::::info
* PHP SQLite and PHP Posix extensions are required since version [8.0.0](https://github.com/bacula-web/bacula-web/releases/tag/v8.0.0)
::::

## SELinux support

For further information on how to setup Bacula-Web on a server with SELinux in enforced mode, see [this page](selinux.md)