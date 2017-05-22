.. _about/project:

=================
About the project
=================

The project story
-----------------

**2004 - 2008**

Bacula-Web has been firstly created arround 2004 by Juan Luis Frances.
He made a really fantastic work and provide to the community a very nice tool that provide usefull informations about Bacula backup jobs, pools and volumes.

Then from 2004 to 2008, the  project was maintened by Juan Luis Frances making bug fixes and improvments.

**2010 - the project revival**

*Why reviving this project ?*

Since November 2010, this project got a new official maintener and a new beginning.

Since several years, I use Bacula for personal and professional purpose.

Then I was looking for a web based tool which can provide me usefull information about last backup jobs, pools usage, volumes, etc.

My first look were on webacula and bweb which are very good tool to use and features full but they look maybe pretty much not easy to install and configure.
Then, I've found Bacula-Web which at this time, wasn't maintened since few years.

After I've submited some patches to Bacula developpers, I proposed to become the official maintainer of this project. So the project revival started on July 2010.

Roadmap
-------

The project development roadmap is available into the `Mantis bug tracker`_

Versions numbering
------------------

*Releases prior 6.0.0*

Bacula-Web version follow Bacula numbering (see example below)

============== ================== 
Bacula version Bacula-Web version 
============== ==================
5.2.13         5.2.13-1
5.2.13         5.2.13-2
5.2.13         5.2.13-3
============== ==================

*Since release 6.0.0*

Why changing release numbering ?

First, to make my developper "life" easier ;)
And to identify clearly which release is major, minor or a maintenance/bug fix release

As of version 6.0.0, each Bacula-Web releases will be composed of three digits (see below)

   * first: major release (major new features)
   * second: minor release (minor new features)
   * third: maintenance and/or bug fix release

For example: Release 6.0.0 (released on November 11th 2013

===== ===== ======
Major Minor Bugfix
===== ===== ======
6     0     0
===== ===== ======

Others good tools
-----------------

I know that there's a lot of web based console for administring, monitoring and configuring bacula such as

   * bacula module in `Webmin`_ 
   * `breport`_ - The Bacula Reporter
   * bat
   * `Webacula`_
   * and many more ...

You will find a complete `list of GUI`_ on the Bacula's web site


.. _Webmin: http://www.webmin.com/index.html
.. _breport: http://breport.sourceforge.net/
.. _Webacula: http://webacula.sourceforge.net/
.. _list of GUI: http://www.bacula.org/manuals/en/console/console/GUI_Programs.html
.. _Mantis bug tracker: http://bugs.bacula-web.org/
