.. _about/about:

Bacula-Web is a php web based tool that provide you a summarized view of jobs, pools and volumes. Its obtain his information from your bacula catalog database.

This tool provide you information on the last day jobs status, medias and pool usage, catalog size usage, and even more ...

All features are described in the :ref:`about/features` page.

The main advantages of Bacula-Web are

   * it's web based, so you can reach it simply through your preferred browser from anywhere
   * it's easy to install (you just need a LAMP server and a valid connection to your Bacula catalog)
   * it contain a lot of information into a single page (have a look on your last jobs for example)

and what's not ?

If you're looking for a tool able to manage Bacula like bat, Bacula-Web might not the right tool (yet).

I'd advise to have a look on Bweb, baculum, BAT or Bacula Webmin plugin instead.

The project history
===================

Bacula-Web was originally created and developed by Juan-Luis Frances Jimenez.
He made a very nice work.

Since the end of 2010, I was officially designed the official maintainer by Kern S. and Eric B. for this project.

Project timeline
****************

**2004 - 2008**

Bacula-Web has been firstly created around 2004 by Juan Luis Frances.
He made a really fantastic work and provide to the community a very nice tool that provide useful information about Bacula backup jobs, pools and volumes.

Then from 2004 to 2008, the  project was maintained by Juan Luis Frances making bug fixes and improvements.

**2010 - the project revival**

*Why reviving this project ?*

Since November 2010, this project got a new official maintainer and a new beginning.

Since several years, I use Bacula for personal and professional purpose.

Then I was looking for a web based tool which can provide me useful information about last backup jobs, pools usage, volumes, etc.

My first look were on webacula and bweb which are very good tool to use and features full but they look maybe pretty much not easy to install and configure.
Then, I've found Bacula-Web which at this time, wasn't maintained since few years.

After I've submitted some patches to Bacula developers, I proposed to become the official maintainer of this project. So the project revival started on July 2010.

Third-party tools and libraries
===============================

Bacula-Web use the following tools and libraries

===========================  ================
Component                    License
===========================  ================
`PHP`_                       PHP License v3.01
`NVD3`_                      Apache v2
`Bootstrap`_                 MIT
`Font Awesome`_              CC BY 4.0
`jQuery`_                    MIT
`tempus-dominus`_            MIT
`Valitron`_                  BSD-3-Clause
`Date_HumanDiff`_            LGPL
`phpdotenv`_                 BSD-3-Clause
`PHP-DI`_                    MIT
`Slim-PHP`_                  MIT
`GuzzleHttp/psr7`_           MIT
`Twig`_                      BSD-3-Clause
`cocur/human-date`_          MIT
`odan/session`_              MIT
`Symfony Console`_           MIT
===========================  ================

Supported browser
=================

Bacula-Web was successfully tested with

   * Mozilla Firefox 94.0.1
   * Google Chrome 95.0.4638.69
   * Brave version 1.31.88
   * Microsoft Edge 95.0.1020.44

.. important:: Bacula-Web needs Javascript and Cookies to run well in your web browser, do not disable one of those.

Translations
============

Bacula-Web language is by default in english, but it's also translated in not less than 15 languages 

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

.. important:: If you want to contribute in maintaining translations for a language or add a new one, have a look at the :ref:`contribute/translations` chapter

About Bacula
============

Bacula is a set of Open Source, enterprise ready, computer programs that permit you (or the system administrator) to manage backup, recovery, and verification of computer data across a network of computers of different kinds. Bacula is relatively easy to use and efficient, while offering many advanced storage management features that make it easy to find and recover lost or damaged files. In technical terms, it is an Open Source, enterprise ready, network based backup program (source `www.bacula.org`_).

IMHO, Bacula is a great open source backup tool (for professional and private purpose)

About myself
============

I've discovered Linux with `Slackware`_ maybe around 1995 ( I know, I'm an old guy).
Since this time, I had some experiences with

   * Enterprise oriented linux distros
   * Database such as MySQL, postGreSQL, Oracle, etc...
   * Backup solution (both proprietary and open source solutions)
   * Security skills
   * Networking skills
   * Programming (Javascript, PHP, C++, Pascal, bash, perl, etc.)

And many more stuff that I'll not describe there (it's not a resume, it's just a simple presentation about myself ;)

My preferred Linux distros is `Gentoo`_ and I use `Centos`_ for labs, development and testing

Others good tools
=================

There are a lot of tool which can help you administering, monitoring and configuring Bacula like

   * Bacula module in `Webmin`_
   * `Bacula Status`_
   * `Reportula`_
   * `baculum`_
   * `breport`_ - The Bacula Reporter
   * bat
   * `Webacula`_
   * and many others ...
     
You can find a complete `list of GUI`_ on the Bacula's web site

.. _PHP: http://www.php.net
.. _NVD3: http://nvd3.org/
.. _Bootstrap: http://getbootstrap.com/
.. _Font Awesome: http://fontawesome.io/
.. _jQuery: http://jquery.com
.. _www.bacula.org: http://www.bacula.org
.. _Slackware: http://www.slackware.com/
.. _Gentoo: https://www.gentoo.org/
.. _Centos: https://www.centos.org/
.. _Webmin: http://www.webmin.com/index.html
.. _Bacula Status: https://github.com/evaldoprestes/baculastatus
.. _Reportula: https://www.reportula.org
.. _baculum: https://www.bacula.org/7.4.x-manuals/en/console/Baculum_Web_GUI_Tool.html
.. _breport: https://breport.sourceforge.net
.. _Webacula: https://webacula.sourceforge.net/
.. _list of GUI: https://www.bacula.org/manuals/en/console/console/GUI_Programs.html
.. _tempus-dominus: https://github.com/Eonasdan/tempus-dominus
.. _Valitron: https://github.com/vlucas/valitron
.. _Date_HumanDiff: https://github.com/pear/Date_HumanDiff
.. _phpdotenv: https://github.com/vlucas/phpdotenv
.. _PHP-DI: https://github.com/PHP-DI/PHP-DI
.. _Slim-PHP: https://github.com/slimphp/Slim
.. _GuzzleHttp/psr7: https://github.com/guzzle/psr7
.. _cocur/human-date: https://github.com/cocur/human-date
.. _odan/session: https://github.com/odan/session
.. _Twig: https://github.com/twigphp/Twig
.. _Symfony Console: https://github.com/symfony/console
