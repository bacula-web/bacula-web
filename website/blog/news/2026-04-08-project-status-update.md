---
title: "Project status update"
date: Wed, 08 Apr 2026 07:45:46 +0200
last_update:
  date: Wed, 08 Apr 2026 08:43:16 +0200
  author: dfranco
description: "Bacula-Web project - status update"
tags:
  - news
authors: dfranco
---

After a long period of inactivity on this project, I would like to share what the current status of the project is (see [#232](https://github.com/bacula-web/bacula-web/issues/232#issuecomment-4047442040)),
why the project activity was a bit low over the last couple of months, and what's coming in the next couple of weeks or months.

<!-- truncate -->

## Current status

As of today, the latest release ([9.9.5](https://github.com/bacula-web/bacula-web/releases/tag/v9.9.5)) was published in March 2026 and included only a fix for the issue ([#278](https://github.com/bacula-web/bacula-web/issues/278)),
and a fix for the documentation, which was lacking instructions on how to use Bacula-Web on a shared web server (such as a VPS hosting several web apps like WordPress, etc.).

For months, I've tried to refactor the current code in a different branch, but for some reason, I got stuck at some points.
Especially with how to handle several Bacula catalog connections within the same Bacula-Web instance.

Also, current code is using the [Slim](https://www.slimframework.com/) PHP framework, which isn't moving forward as fast as I was expecting, and putting me in the situation where
I have to maintain and implement a lot of features by myself (as related project(s) does not look to be maintained anymore).

So now, I'm afraid I don't have other choices other than moving forward with the "big bang" code refactoring so I can publish the next major release (10.0.0).

## What's coming next ?

### 10.0.0-alpha release

The first alpha release (10.0.0-alpha1) will include **many breaking changes**.

Expected **breaking changes**

* Required PHP version >= 8.1
* No more config.php to store the general or user settings
* npm cli must be installed (but **only** if you plan to install from source)
* Translations are not managed with Transifex anymore, but with Lokalize (more details will be included in the 10.x documentation).

**Missing features**

Expect some broken or missing features in the first alpha releases.

One for sure will be the ability to set up several Bacula catalogs on the same instance, but this "feature" will be implemented later in a next (beta or rc) release.

**Expected release date**

The first alpha release should be available in the next 2 weeks (by end of April), maybe before if I can allocate more time.

I will keep the documentation for version 10.0.0 as simple as possible to minimize the efforts, but it will include all the necessary steps to set up, configure, and use it.
It will also include all the instructions to upgrade from a previous version (9.x).

## Feedback

As usual, I appreciate every feedback from the community, so don't hesitate to create a new thread in the [GitHub discussion](https://github.com/bacula-web/bacula-web/discussions) channel.

## Thanks

Once again, thanks for using and supporting the Bacula-Web project, this project wouldn't exist without all of you.

> P.S: I'll keep this post updated as soon as I've important information to share with the community.