.. _contribute/development:

============
Development
============

Bacula-Web source git repository is publicly available and kindly hosted by GitHub `here <https://github.com/bacula-web/bacula-web>`_

Guidance for developers
========================

Please find below some guidance for developers

   * First, please open a bug on `GitHub issues`_ (important for me to track changes in the code)
   * clone the **develop** git branch and give it a useful name such as
      * bugfix-xxx to fix a bug
      * feature-yyy for a new feature
   * do not create "huge" pull request, I do prefer as small as possible pull request
   * do not change the code indentation in your commit
   * I try to apply `PSR-12 coding style standard <https://www.php-fig.org/psr/psr-12/>`_, please sure your commit(s) uses the same standard
   * I take care of code indentation before each release and it's easier for me to see the changes you've done
   * put useful comment in the code that explain what you're trying to do
   * choose good name for variables
   
**Important:** As I don't want to waste your time, before changing any tool or library, make sure it's compatible with Bacula-Web license (GPL v2).
I've seen several people requesting a pull request but the tool license was not compatible with GPL, or even worst, not open source at all.

Thanks for your help.

Shall you have any questions, feel free to get back to me by mail.

.. _GitHub issues: https://github.com/bacula-web/bacula-web/issues
