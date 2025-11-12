# Reporting an issue

Here is some guideline to report a new issue

## Before submitting an issue

Make sure that

- you had a look at the [FAQ page](faq.md), there might be some useful information which can solve your problem
- you're using [latest Bacula-Web release](https://github.com/bacula-web/bacula-web/releases/latest)
- the `application/config/config.php` has been copied from `application/config/config.php.sample`
  and you've at least one catalog connection setup 
- the config file have been adapted to your configuration (check the documentation)
- all items are ok in the test page (except for the database engine you don't use)
- you checked Apache error log file for some warnings or errors
- you're able to connect to the database with the client
  - `mysql` for MySQL/MariaDB
  - `psql` for postgreSQL
  - `sqlite3` for SQLite

Then make sure you include those details in the bug report

* PHP version
* Which database you're using (SQLite, MySQL or postgreSQL)
* Apache (or any web server) logs
* Screenshot(s) are useful too sometime

::::tip
As many details you'll provide, as fast I could help you :)
::::
