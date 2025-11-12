# Contribute to the source code

This page gives further information on how to contribute to the development of the Bacula-Web source code.

## Project source code

Bacula-Web source git repository is publicly available on GitHub [here](https://github.com/bacula-web/bacula-web)

## How to fix a bug, or add a new feature

The usual steps are

* Open an issue on [GitHub](https://github.com/bacula-web/bacula-web/issues) (mandatory)
* Fork the project into your GitHub account
* Clone the forked dev-9.x branch from your account
  
  ```shell
  git clone git@github.com:<account-name>/bacula-web.git -b dev-9.x
  ```
* Checkout the dev-9.x branch (from your own repo) and name it `feat/xyz` or `fix/xyz`
  ```shell
  git checkout -b feat/awesome-feature
  git checkout -b fix/some-bug
  ```
* Once you're happy with your changes, make sure your code follow PSR-12 standard
    * To check coding standard
      ```shell
      vendor/bin/phpcs <path-to-changed-files>
      ```
    * To fix code if needed, run
      ```shell
      vendor/bin/phpcbf <path-to-changed-files>
      ```

## Some rules

- Use [conventional commit](https://www.conventionalcommits.org/en/v1.0.0/) for commit message (mandatory)
- Do not create a "huge" pull request (split changes into several pull requests if possible)
- Fix code indentation ONLY for the code you've changed or added
- Use [PSR-12 coding style standard](https://www.php-fig.org/psr/psr-12/), please make sure you've run `phpcs` and `phpcbf` mentioned above
- Create a useful commit message
  - Bad commit message 
    ```shell
    fix: fix line 128 in index.php
    
    Fix the line 128, as there was something broken here.
    ```
  - Good commit message
    ```shell
    fix: job list return type
    
    Fix return type in SomeClass::getjobs().
    ```
- Use [camel Case](https://en.wikipedia.org/wiki/Camel_case) for variable, method or function names.
- Make sure you don't use dependencies with a license not compatible with the actual project license (GPL-v2)

Thanks for your help and contribution ❤️

Should you have any questions, feel free to create a new discussion on the [GitHub project](https://github.com/bacula-web/bacula-web/discussions)
