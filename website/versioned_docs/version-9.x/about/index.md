---
slug: /about
description: some information about the Bacula-Web project
---

# About the project

## What is Bacula-Web?

Bacula-Web is a PHP web-based tool that provides you with a summarized view of jobs, pools, and volumes. It obtains its information from your Bacula catalog database.

This tool provides you with information on the last day's jobs status, media and pool usage, catalog size usage, and even more ...

All features are described in the [features](/docs/about/features) page.

The main advantages of Bacula-Web are

* it's web based, so you can reach it simply through your preferred browser from anywhere
* it's easy to install (you just need a LAMP server and a valid connection to your Bacula catalog)
* it contains a lot of information into a single page (have a look on your last jobs for example)

## What's not?

If you're looking for a tool able to manage Bacula like bat, Bacula-Web might not the right tool (yet).

I'd advise having a look on Bweb, baculum, Bacularis, BAT or Bacula Webmin plugin instead.

## The project history

Bacula-Web was originally created and developed by Juan-Luis Frances Jimenez, who did a fantastic job.

Since the end of 2010, I was officially designated the official maintainer by Kern S. and Eric B. for this project.

### Project timeline

**2004 - 2008**

Bacula-Web was first created around 2004 by Juan Luis Frances.
He made a really fantastic work and provided to the community with a very nice tool that provide useful information about Bacula backup jobs, pools, and volumes.

Then, from 2004 to 2008, the  project was maintained by Juan Luis Frances, making bug fixes and improvements.

**2010 - the project revival**

*Why reviving this project ?*

Since November 2010, this project has had a new official maintainer and a new beginning.

For several years, I have been using Bacula for personal and professional purposes.

Then I was looking for a web-based tool that could provide me with useful information about last backup jobs, pools usage, volumes, etc.

My first look was on webacula and bweb, which are very good tools to use and features full but they look maybe pretty much not easy to install and configure.
Then, I've found Bacula-Web, which at this time, wasn't maintained for a few years.

After I've submitted some patches to Bacula developers, I proposed to become the official maintainer of this project. So the project revival started in July 2010.

I do use Bacula-Web for personal usage every single day, and the idea was simply to share those improvements with the Bacula community

## Third-party tools and libraries

Bacula-Web uses the following tools and libraries.

| Component                                                    | License           |
|--------------------------------------------------------------|-------------------|
| [PHP](http://www.php.net)                                    | PHP License v3.01 |
| [NVD3](http://nvd3.org/)                                     | Apache v2         |
| [Bootstrap](http://getbootstrap.com/)                        | MIT               |
| [Font Awesome](http://fontawesome.io/)                       | CC BY 4.0         |
| [jQuery](http://jquery.com)                                  | MIT               |
| [tempus-dominus](https://github.com/Eonasdan/tempus-dominus) | MIT               |
| [Valitron](https://github.com/vlucas/valitron)               | BSD-3-Clause      |
| [phpdotenv](https://github.com/vlucas/phpdotenv)             | BSD-3-Clause      |
| [PHP-DI](https://github.com/PHP-DI/PHP-DI)                   | MIT               |
| [Slim-PHP](https://github.com/slimphp/Slim)                  | MIT               |
| [GuzzleHttp/psr7](https://github.com/guzzle/psr7)            | MIT               |
| [Twig](https://github.com/twigphp/Twig)                      | BSD-3-Clause      |
| [cocur/human-date](https://github.com/cocur/human-date)      | MIT               |
| [odan/session](https://github.com/odan/session)              | MIT               |
| [Symfony Console](https://github.com/symfony/console)        | MIT               |
| [nesbot/carbon](https://github.com/briannesbitt/Carbon)      | MIT               |

## Supported browser

Bacula-Web was successfully tested with

* Mozilla Firefox
* Google Chrome
* Brave version
* Microsoft Edge

::::tip
Bacula-Web needs JavaScript and Cookies to run well in your web browser (do not disable one of those).
::::

## Translations

Bacula-Web language is by default in English, but it's also translated into not least 15 languages.

* Belarusian
* Catalan
* Chinese
* Dutch
* English
* French
* German
* Italian
* Japanese
* Norwegian
* Polish
* Portuguese Brazil
* Romanian
* Russian
* Spanish
* Swedish

I would like to say **thank you very much** to all people involved in Bacula-Web translations.
You're all doing a fantastic job !!!

::::tip
If you want to contribute to maintaining translations for a language or add a new one, have a look at the [contribution page](/docs/contribute/translations).
::::

## About Bacula

Bacula is a set of Open Source, enterprise-ready computer programs that permit you (or the system administrator) to manage backup, recovery, and verification of computer data across a network of computers of different kinds. Bacula is relatively easy to use and efficient, while offering many advanced storage management features that make it easy to find and recover lost or damaged files. In technical terms, it is an Open Source, enterprise-ready, network-based backup program (source [www.bacula.org](https://www.bacula.org)).

IMHO, Bacula is a great open source backup tool (for professional and private purposes)

## About me

I've discovered Linux with [Slackware](http://www.slackware.com/) maybe around 1995 ( I know, I'm an old guy).
Since this time, I have had some experiences with

* Enterprise oriented linux distros
* Database such as MySQL, postGreSQL, Oracle, etc...
* Backup solution (both proprietary and open source solutions)
* Security skills
* Networking skills
* Programming (Javascript, PHP, C++, Pascal, bash, perl, etc.)

And many more things that I'll not describe there (it's not a resume, it's just a simple presentation about myself ;)

My preferred Linux distros is [Gentoo](https://www.gentoo.org/) and I use [Centos](https://www.centos.org/) for labs, development and testing

## Other good tools

There are a lot of tools which can help you administer, monitor and configure Bacula like

* Bacula module in [Webmin](http://www.webmin.com/index.html)
* [Bacula Status](https://github.com/evaldoprestes/baculastatus)
* [breport](https://breport.sourceforge.net) - The Bacula Reporter
* bat (Bacula Admin Tool GUI)
* [Webacula](https://webacula.sourceforge.net/)
* [Reportula](https://github.com/oliveiraped/Reportula)
* [Baculum](https://www.bacula.org/15.0.x-manuals/en/console/Baculum_API_Web_GUI_Tools.html)
* [Bacularis](https://bacularis.app/)
* and many others ...

You can also find a complete list of GUIs on Bacula's website.
