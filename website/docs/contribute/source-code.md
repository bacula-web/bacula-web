# Contribute to source code

Bacula-Web source git repository is publicly available and kindly hosted by GitHub [here](https://github.com/bacula-web/bacula-web)

## Guidance for developers

This section describe how you can contribute to Bacula-Web project development.

**Usual workflow**

* Open a bug on [GitHub](https://github.com/bacula-web/bacula-web/issues) (mandatory)
* Create a new fork of the project into your GitHub account
* Clone the forked 10.x branch from your account
  `git clone git@github.com:<account-name>/bacula-web.git`
* create a branch from the **10.x** branch and give it a name that follow conventional commit guideline
    * fix/fix-the-output
    * feat/new-feature
      `git checkout -b fix/fix-the-output`
* Once you're happy with your changes, make sure your code follow PSR-12 standard
    * To check coding standard `vendor/bin/phpcs <path-to-changed-files>`
    * To fix code if needed, run `vendor/bin/phpcbf <path-to-changed-files>`
* do not create "huge" pull request, I do prefer as small as possible pull request
* do not change the code indentation in your commit
* I try to apply [PSR-12 coding style standard](https://www.php-fig.org/psr/psr-12/), please sure your commit(s) uses the same standard
* I take care of code indentation before each release and it's easier for me to see the changes you've done
* put useful comment in the code that explain what you're trying to do
* choose good name for variables

:::tip
As I don't want to waste your time, before changing any tool or library, make sure it's compatible with Bacula-Web license (GPL v2).
I've seen several people requesting a pull request but the tool license was not compatible with GPL, or even worst, not open source at all.
:::

Thanks for your help.

Shall you have any questions, feel free to create a new discussion on the [GitHub project](https://github.com/bacula-web/bacula-web/discussions)
