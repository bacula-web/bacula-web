---
title: "Composer installation broken"
date: 2019-01-25 18:21:29 +0000
tags:
  - news
authors: dfranco
---

Please note that the installation of [Bacula-Web using Composer](https://www.bacula-web.org/docs/install/composer-install) is currently broken due to missing Composer dependency.

<!-- truncate -->

As you can see below, It seems that [bootstrap-validator](https://github.com/1000hz/bootstrap-validator) package have been abandoned and is not available anymore from [Packagist.org](https://packagist.org/)

```shell
$ composer create-project --prefer-dist bacula-web/bacula-web bacula-web
Installing bacula-web/bacula-web (v8.2.0)
  - Installing bacula-web/bacula-web (v8.2.0)
    Downloading: 100%

Created project in bacula-web
Loading composer repositories with package information
Installing dependencies (including require-dev) from lock file
  - Installing 1000hz/bootstrap-validator (v0.11.9)
    Downloading: Failed
    Failed to download 1000hz/bootstrap-validator from dist: The "https://api.github.com/repos/EvilFreelancer/bootstrap-validator/zipball/dfa97d6cbde5255010ac398fa4628d62ed2b1f67" file could not be downloaded (HTTP/1.1 404 Not Found)
    Now trying to download from source
  - Installing 1000hz/bootstrap-validator (v0.11.9)
...
```

I'll try to mitigate the problem and release a minor bug fix release asap.

Davide
