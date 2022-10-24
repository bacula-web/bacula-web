## Bacula-Web 8.6.2 (October 24th 2022)

### Changelog

- General
  - Fixed a regression introduced in v8.6.1
    Using Bacula-Web console to setup authentication was throwing an error
    Big thanks to @IdahoPL for reporting the issue

### Fixed bug(s)

- #127 [bugfix] - Failed to start the session because headers have already been sent
- #128 [bugfix] - Database class constructor tight coupled from Session

### New feature(s)

- none

## Bacula-Web 8.6.1 (October 19th 2022)

### Changelog

- General
  - User will stay on current page when switching to another catalog (see #22)
  - Fixed Bacula catalog selector
    Using several Bacula catalog, the catalog selector was broken for some pages (see #120)
  - Web browser will not ask a form submission while moving back from Job logs report page (see #30)
  - Fixed how pagination count rows per page (see #123)
  - Bump Composer dependencies to latest version
  - Fixed pagination next button (see #125)
  - Link to official documentation is now available from top navigation bar

- Jobs report
  - Fixed pagination not using applied filters and options (see #122)
  - Job endtime filter is now working as expected
  - Ordering options are kept while using pagination (see #122)
  - You can reach Job files report from Jobs report page from now (see #126)

- Job logs report
  - More job information are now displayed (see #124)

- Pools report
  - Fixed the Volumes button which now list volumes in the right pool (see #121)

- Volumes report
  - Fixed pagination not using applied filters and options (see #122)

- Settings
  - Removed extra flash message which appear when a user is created

### Fixed bug(s)

- #22 [bugfix] - Stay on current page when switching to another catalog
- #30 [bugfix] - Make it possible to go back from jobs details to (filtered) job overview
- #120 [bugfix] - Bacula catalog selector not working
- #121 [bugfix] - Listing volumes for specific pool does not work
- #122 [bugfix] - Page filters and options are lost using pagination
- #123 [bugfix] - Incorrect displayed rows per pagination page
- #125 [bubfix] - Next button in pagination should display next pagination page

### New feature(s)

- #124 - Display more job information on Job logs report
- #126 - Show files of backup jobs in Jobs report page

## Bacula-Web 8.6.0 (October 6th 2022)

### Changelog

- General
  - **Breaking change**: Implemented front controller
    Bacula-Web web app is now served from the public sub-folder, please
    check the web server config documentation (see #114)
  - Replaced CHttpRequest class by Symfony framework Request class
  - Use combined operators in CUtil class (see #91)
  - Refactored PDO related PHP classes (see #100)
  - Updated composer.json by adding ext-pdo dependency (see #113)
  - Updated allowed plugin in composer.json (see #103)
  - Use PHP namespaces (see #118)
  - Fixed dozens of code smell warnings
  - Refactored good amount of the code
  - Moved flash message below header for better visibility
  - Improve handling of non-existant page requests
  - Fixed PHP notice after login (see #117)

- Test page
  - Fixed wrong link to test page on error page

- Translations
    - Updated translations with one more language (romanian) (see #92) 

- Security
  - Bump smarty/smarty from 3.1.45 to 3.1.47 (see #116)
  - Improved session management (see #68)
  - Improved how user input are sanitized (see #86)

- Documentation
  - Fixed link to contributors on README
  - Updated list of components with license
  - Updated Apache and Nginx server configuration according to new public root folder (see #114)

### Fixed bug(s)

- #68 [security] - Improve session managemen
- #86 [security] - Sanitize user input
- #91 [enhancement] - Use combined operators (thanks to @elfring)
- #92 [translation] - Update translations
- #100 [enhancement] - Refactor PHP PDO database related classes
- #103 [enhancement] - Improve support Composer version 2.2.1 or later
- #113 [bugfix] - Add missing ext-pdo to composer.json
- #114 [security] - Implement front controller
- #116 [security] - Bump smarty/smarty from 3.1.45 to 3.1.47
- #117 [bugfix] - PHP notice about undefined catalog_id
- #118 [enhancement] - Use PHP namespaces

### New feature(s)

- none

## Bacula-Web 8.5.5 (June 4th 2022)

### Changelog

- General
	- Fix security issue with smarty/smarty (see #111)

### Fixed bug(s)

- #111 [security] - Upgrade smarty/smarty to 3.1.45

### New feature(s)

- none

## Bacula-Web 8.5.4 (May 21st 2022)

### Changelog

- General
  - Removed DataTables from everywhere (see #107)

- Backup job report
  - Fixed a bug while displaying compression ratio for canceled jobs (see #105)

- Client report
  - Fixed SQL query error using custom datetime format in Client report page (see #106)

### Fixed bug(s)

- #105 [bug] - Division by zero with canceled jobs inBackup Job report
- #106 [bug] - SQL query error using custom datetime format in Client report page
- #107 [improvement] - Remove datatables dependency and related code
- #108 [bug] - Broken job status link in last period job status

### New feature(s)

- none

## Bacula-Web 8.5.3 (March 9th 2022)

### Changelog

- General
  - GPL license notice has been updated in all files
  - Minor code refactoring and code smell improvments
  - Fixed type and requirements in composer.json

- Jobs report
  - Fixed bug while using several filter in jobs report page (see #99)

- Volumes report
  - Fixed bug while using several filter in volume report page (see #101)

- Security
  - Smarty template engine has been updated to version 3.1.43 (see #104)

- Documentation
  - Fixed composer install command by adding --no-dev and removing --prefer-dist (by default)

### Fixed bug(s)

- #99 [bug] - Database error while using filters in Jobs report
- #101 [bug] - Query error while filtering volumes
- #104 [security] - Upgrade Smarty to version 3.1.43

### New feature(s)

- none

## Bacula-Web 8.5.2 (December 15th 2021)

### Changelog

- General
    - Fixed SQL Error using PHP 8.0 and latest version of MariaDB (see #97 and #98)
    - Updated Composer dependencies to latest version

### Fixed bug(s)

- #97 [bug] - SQL Error HY093 on first page load (thanks to @dandrzejewski)
- #98 [bug] - CModel::run_query() does not reset PHP PDO bind parameters

### New Feature(s)

- none

## Bacula-Web 8.5.1 (December 6th 2021)

### Changelog

- General
	- Fixed / Improved PostgreSQL Bacula catalog support (see #93, #94 and #95)

- Jobs report
	- Fixed regression using PostgreSQL bacula catalog (see #93)
	  Thanks to @lrosenman for the bug report
	- Improved PostgreSQL Bacula catalog support

- Client report
	- Fixed / Improved PostgreSQL Bacula catalog support (see #95)

- Backup job report
	- Fixed / Improved PostgreSQL Bacula catalog support (#94)
	- Completed backup jobs list are now displayed within the selected period (#96)
	  Previous versions were only displaying the last completed job for a specific client

- Documentation
	- Update Client report page screenshot

- Security
	- Enforced use of PHP PDO bind parameters for user input

### Fixed bug(s)

- #93 [bug] - Bad sql query on Jobs report page using version 8.5.0
- #94 [bug] - BackupJob report error using PostgreSQL catalog
- #95 [bug] - Bad SQL query on Clients report page using PostgreSQL
- #96 [bug] - Backup jobs are not displayed for the selected period

### New feature(s)

- none

## Bacula-Web 8.5.0 (December 4th 2021)

### Changelog

- General
  	- Updated README.md with more information, screenshots, etc.
  	- Fixed Composer dependencies (see #80)
  	- Fixed bwc error message while using unknow sub-command (see #81)
	- Fixed links in error page (see #89)
	- Pagination on Volumes and Jobs report pages are now performed on server side (see #66)
	  Thanks to @ibowen for the bug report
	- Improved Error page design

- Job files report
  	- Fixed regression browsing backup job files with older Bacula catalog (see #83)

- Documentation
  	- Added instructions about PHP session path on server running with SELinux enforced (see #63)
  	- Fixed ReadTheDocs builds (see #78)
	- Updated Apache and Nginx configuration
	- Fixed a dead link to the old bug tracker (see #88)

- Development
  	- Upgraded smarty/smarty to version 3.1.40 (see #75)

- Security
  	- Improved how user input from Login form are sanitized (see #77)
	- PHP >= 7.3 is the minimal supported version (see #79)
	  For more informations, see [Supported PHP version](https://www.php.net/supported-versions.php) on https://php.net
	- Prevent directory listing (see #85)

### Fixed bug(s)

- #63 [documentation] - Update SELinux related's documentation
- #60 [bug] - On paginations of job and volume views
- #66 [bug] - Fix volumes and jobs pagination
- #75 [enhancement] - Upgrade Smarty to latest release
- #77 [security] - Improve user input sanitisation in login form
- #78 [documentation] - Fix documentation builds
- #79 [security] - Deprecate PHP versions prior to 7.3
- #80 [bug] - Update Composer dependencies
- #81 [bug] - Wrong command file name in bwc
- #82 [bug] - PHP Notice about undefined index graph_jobs
- #83 [bug] - Broken job files pagination using older Bacula catalog
- #84 [bug] - PHP notices in Settings page 
- #85 [security] - Prevent directory listing
- #87 [documentation] - Update Apache and Nginx documentation
- #88 [documentation] - Fix link to bug tracker
- #89 [bug] - Fix links in error page

### New feature(s)

- none

## Bacula-Web 8.4.4 (October 10th 2021)

### Changelog

- Job files report
   - Job files report now works using latest Bacula version (<= 11.0.x)
   - Pagination while browsing backup job file(s= have been fixed

- Development
   - Several fixes and refactoring

### Fixed bug(s)

- #71 [bug] - Fix bacula 11.0 database schema compatibility
- #73 [bug] - Fix pagination in JobFiles report
- #74 [bug] - Deprecated error while using several Bacula catalog

### New feature(s)

- none

## Bacula-Web 8.4.3 (October 3rd 2021)

**Important note**

There's a known regression with JobFile and BackupJob reports page for users using Bacula >= 11.0.0 and a bug is already open (see #71 ). 
I'v already started to work on fixes, which should be available in a next release.

Thanks to @tolland for submitting the bug report.

### Changelog

- Documentation
	- Add "support the project" section to README.md
	- Update documentation about non-default language configuration
	- Remove old Mantis bug tracker from contribution guide
	- Several updates and fixes

- Miscelanous
	- General code refactoring and improvments
	- Make PHP code PSR-2 compliant

- Development
	- Add friendsofphp/php-cs-fixer to Composer require-dev
	- Update Composer dependencies

### Fixed bug(s)

- #64 [documentation] - Update documentation related to translations
- #65 [documentation] - Update contribution guide
- #69 [improvement] - Make code PSR-2 compliant

### New feature(s)

- none

## Bacula-Web 8.4.2 (July 11th 2021)

### Changelog

- General
	- Upgrade Composer dependencies

- Documentation
	- Fix copyright
	- Minor fixes
	- Add notice about latest Composer version

- Miscelanous
	- Fix homepage in composer.json to use https
	- Fix several PHP notices
	- General code improvements
	- Upgrade jquery to version 3.5.1
	- Upgrade datatables/datatables to version 1.10.21
	- Upgrade smarty-gettext/smarty-gettext to version 1.6.2
	- phpmd/phpmd to version 2.10.1
	- squizlabs/php_codesniffer to version 3.6.

### Security
	- Upgrade Smarty to version v3.1.39 to fix CVE-2021-26119 and CVE-2021-26120 (see #58)

### Fixed bug(s)

- #58 [security] - Upgrade Smarty to latest stable version

### New feature(s)

- none

## Bacula-Web 8.4.1 (October 24th 2020)

### Changelog

- Job file report
	- Fix files count (#55)
	- Search form looks for path or filename matches

- Translations
	- Update translations (#56)

### Fixed bug(s)

- #55 [bug] - Job files search returns SQL syntax error
- #56 [translation] - Update translations

### New feature(s)

- none

## Bacula-Web Release 8.4.0 (June 1st 2020)

### Changelog

- Jobs report
	- Fix start time and end time for waiting and running jobs (#46)

- Job files report
	- Add file/folder search form (#54)
	  You can now filter job file/folder list using search field

- Documentation
	- Fix broken download link in Readme.md (#49)
	- Update ownership and permissions chapter (#52)
	- Update supported PHP version (up to 7.3) (#47)
	- Update documentation for Linux Mint (#48)

### Fixed bug(s)

- #46 [bug] - Wrong start time and end time for waiting and running jobs
- #47 [bug] - PHP 7.2 support
- #48 [doc] - PHP SQLite support not found on Linux Mint
- #49 [doc] - Broken dowload link in Readme.md 
- #52 [doc] - Update documentation related to ownership and permissions

### New feature(s)

- #44 [feature] - List of files too short, not configurable
- #54 [feature] - Add search form in Job files report

## Bacula-Web 8.3.3 (December 1st 2019)

### Changelog

- General
	- Bacula-Web tar.gz and SHA sums are now hosted by GitHub
	  Please read latest documentation (http://docs.bacula-web.org)

- Documentation
	- Fix installation from archive (thanks to @Gabrielsc)
	- Add Contributing page (CONTRIBUTING.md) 
	- Minor fixes and improvements 

- Development
	- Improve code in DateTimeUtil::Get_Elapsed_Time() (thanks to @bulanh)
	- Usual PHP code improvements and fixes

### Fixed bug(s)

- #37 [bug] Fix installation from archive in documentation
- #42 [bug] Invalid date format inside DateTimeUtil::Get_Elapsed_Time

### New feature(s)

- none

## Bacula-Web 8.3.2 (August 15th 2019)

### Changelog

- General
	- Fixed bwc (console) usage command
	- Fixed displayed version in console

- General settings
	- Fixed missing debug mode value in general settings

- Jobs report
	- Fixed Bootlint warning about temlplate html code
	- Fixed division by zero while not using compression

- Job files report
	- Fixed postgreSQL support

- Documentation
	- Fixed bug tracker information (using Github issues now)
	- Minor fixes

### Fixed bug(s)

- #36 [bug] Division by zero in Jobs report
- #39 [bug] Error with postgreSQL using JobFiles report page
- #40 [bug] Missing debug mode value in general settings
- #41 [bug] Wrong version displayed in console

### New feature(s)

- none

## Bacula-Web 8.3.1 (April 22nd 2019)

### Changelog

- General
	- Fixed PHP warning on user signout (#0296)

- Dashboard
	- Added tooltip info on some widgets

- Jobs report
	- Fix error on some restore jobs level (#0297)

- Backup job report
	- Now display job status in last backup jobs list (#0299)

- Security
	- Fixed XSS security issue in Bootstrap (#0298)

### Fixed bug(s)

- 0000296 [bug-php] NOTICE type error after user sign out
- 0000297 [bug-php] Errors on restore jobs level in jobs report page
- 0000298 [security-issue] Upgrade Bootstrap to version 3.4.1

### New feature(s)

- 0000299 [feature] Display job status in last backup job (Backup job report)

## Bacula-Web 8.3.0 (March 16th 2019)

### Changelog

- Dashboard
	- Biggest backup job widget is fixed (#0262)
	- Clicking on pie charts now leads to the related reports page (#0255)

- Backup job report
	- You can now list folders and files from a backup job (#0289)

- Job report
	- Fixed PHP warning (division by zero) with canceled jobs (#0290)

- Translations
	- Fixed language translations (#0291)

- Documentation
	- Installation page index has bee reviewed and shows now the whole table
	of content
	- Updated documentation for Fedora (#0293) 
	- Small fixes and improvements

- Development
	- Usual PHP code improvements and fixes

### Fixed bug(s)

- 0000262 [bug-php] Biggest jobs size is total job size
- 0000290 [bug-php] Division by zero with backup job compression
- 0000291 [bug] Language translations doesn't work anymore
- 0000293 [documentation] Update documentation for Fedora

### New feature(s)

- 0000255 [feature] Dashboard: Clicking on numbers and graphs should display report
- 0000289 [feature] Implement file listing within a job

## Bacula-Web 8.2.1 (Jan 28th 2019)

### Changelog

- General
	- Fixed missing bootstrap-validator package using Composer (#0288)
	- Fixed issue with missing formatting information while using Bacula-Web in
	a different language (#0286)
	
- User settings
	- Fixed missing email address in user settings (#0287)

- Documentation

- Development
	- Minor PHP code improvements and fixes

- Fixed bug(s)

- 0000286 [bug-php] Handle missing localized numeric and monetary formatting information
- 0000287 [bug-php] Email address not displayed in user settings page
- 0000288 [packaging] Fix missing bootstrap-validator using Composer

## Bacula-Web 8.2.0 (January 6th 2019)

## Changelog

- General
	- Updated .htaccess adding Options +FollowSymLinks
	- Debug mode is now available (see #0271)
	- Error message arise if user auth backend is not ready and user
	authentication is enabled (see #0274) 

- Security
	- Smarty 3.x security issue - CVE-2018-13982 (see #0276)
	Bacula-Web is not impacted by this security issue
	- User authentication can now be disabled (see #0239)

- Documentation
	- Fixed instructions to download tar.gz archive
	- Documentation have been impproved with a good amount of fixes and
	improvements
	- Updated Composer manual installation (redirect to Composer.org web site)
	- Upgrade paragraph has been fixed (see #0275)
	- Add missing package installation for RHEL/Centos 6 users (see #0283)

- Development
	- Upgraded symfony/process to version 3.4.19
	- Upgraded Smarty and Smarty-Gettext components
	- Huge amount of PHP code improvements and fixes

### Fixed bug(s)

- 0000254 [bug-php] Warnings with PHP 7.0.27
- 0000273 [documentation] Instructions to download archive not accurate
- 0000274 [bug-design] Warn users if authentication back-end db does not exist
- 0000275 [documentation] Fix incomplete documentation for upgrade
- 0000276 [security-issue] Smarty - Fix CVE-2018-13982
- 0000282 [documentation] Update outdated Composer installation
- 0000283 [documentation] Update PHP package instructions for RHEL/Centos 6 users

### New feature(s)

- 0000239 [feature] Disable user authentication
- 0000271 [feature] Add debug mode on application level

## Bacula-Web 8.1.0 (November 7 2018)

### Changelog

- General
	- Jobs overview, volumes and pools report pages can now be paginated
	- the config option <jobs_per_page> is not used anymore, it will not be
	visible anymore in General settings page
	- Link to GitHub project added to header menu
	- New tool bwc (Bacula-Web console). User authentication back-end is now setup by the user after installation
	  More details here http://docs.bacula-web.org/en/latest/02_install/finalize.html#install-finalize
	- For people using Apache web server, mod_rewrite is part of requirement
	- Jobs, Pools and Volumes report pages are now paginated
	- Pie charts now does not display value with decimal

- Jobs report page
	- Jobs with duration > 1 day now display correct duration
	- Disabled link to backup job report if job type is not backup
	- Add missing status (Verify found differences) for Verify jobs

- Test page
	- Remove useless test for pear-db

- Volumes report
	- Add missing status icon for "Used" and "Purged" volumes
	- New: Filter volumes which are in the changer (library)
	- New: Filter volumes by pool

- Settings
	- Fixed "General" and "Users" tabs

- Documentation
	- Add missing permissions steps in "Install from archive" chapter
	- General improvements and content update
	- Update final step (use of Bacula-Web console)
	- Fixed/updated instruction for permissions in <Install from archive>
	chapter

- Development
	- Add datatables/datables Composer requirement
	- Update JQuery from version 3.2.1 to 3.3.1
	- Update moment/moment from version 2.20.1 to 2.22.2
	- Update symfony/process from version 3.4.4 to 3.4.12
	- General code improvements

### Fixed bugs

- 0000220 [bug-php] Pie charts values should not use decimal
- 0000251 [bug-php] Job duration > 1 day looking weird
- 0000252 [documentation] Missing permissions steps in Install from archive chapter
- 0000264 [bug-php] Only see volume status icon when status is append
- 0000268 [bug] Problem using job name link if not backup job
- 0000269 [bug-php] Status icon not displayed for verify jobs
- 0000270 [bug-html] Tabs doesn't work in Settings page

### New feature(s)

- 0000141 [feature] Pagination in report pages
- 0000244 [feature] Filter volumes in changer
- 0000263 [feature] Filter volumes by pool on Volumes report

This release fixes 7 bugs and add 3 new features

## Bacula-Web 8.0.1 (June 22nd 2018)

### Changelog

- Documentation
	- Fix issue with ReadTheDocs theme conflict

Fixed bugs

- 0000248 [documentation] Documentation issue

## Bacula-Web 8.0.0 (June 1st 2018)

### Changelog

- General
	- Breacrumbs navigation has been fixed
	- Application error/exception look and feel have been improved
	- PHP Posix support is now required
- Development
	- Large amount of code improvement and cleanup
- Documentation
	- Add user settings and general settings in features
	- Documentation got cleaned up, restructured and updated

### Fixed bugs

- 0000234 [bug-php] Directors report page problem if a Bacula catalog is unreachable
- 0000241 [bug-php] Breadcrumbs navigation is broken 

### Improvement(s)

- 0000161 [enhancement] Improve application errors/warnings exception message
- 0000222 [enhancement] Improve exception handling

## Bacula-Web 8.0.0-rc3 (March 17 2018)

### Changelog

- User and general settings
	- Configuration settings are now visible in General settings

- Documentation
	- Composer installation have been fixed and updated
	- Nginx web server setup have been added

- Development
	- Code cleanup and few improvements

### Fixed bugs

- 0000242 [bug] Wrong link to test page in Exception page

### Documentation

- 0000217 [documentation] Update documentation with Nginx and Apache server configuration
- 0000238 [documentation] Unable to install Bacula-Web using Composer
- 0000240 [documentation] Unable to install via Composer

## Bacula-Web 8.0.0-rc2 (March 2nd 2018)

### Changelog

- General
	- Users authentication feature added

- Test page
	- Check if application/assets/protected is writable

- User and general settings
	- Users can reset their password
	- Possibility to add more users from general settings page

- Security
	- Fixed many SQL injection and XSS vulnerabilities (see fixed bugs)

- Development
	- Large amount of code improvement and cleanup
	- Upgrade moment/moment to version 2.19.2
	- Add Bootstrap validator to Composer's requirements

### Fixed bug(s)

- 0000211 [security-issue] SQL Injection in jobs.php
- 0000226 [bug] Clicking job name in jobs.php gives http error 500
- 0000227 [bug-php] Deprecated split() function with PHP-7.0 in BWeb class
- 0000230 [bug-php] Exception message Language translation problem
- 0000231 [bug-php] Volume slot number should be set to n/a if not in changer
- 0000232 [bug-php] Error while ordering volumes
- 0000233 [bug] Wrong empty volume's date
- 0000236 [security-issue] - XSS vulnerabilities in jobs page

### New feature(s)

- 0000092 [feature] Implement user authentication

### Translations

none

### Documentation

	- Bacula-Web can be installed using the archive, or via Composer
	- Documentation got cleaned up, restructured and updated

## Bacula-Web 8.0.0-rc1 (October 19th 2017)

### Changelog

- General
	- PHPLot has been replaced by nvd3
	- Use Composer for libs dependencies and installation (Bacula-Web is
	available on packagist.org
- Directors report
	- New directors report page display high level statistics of all
	configured Bacula directors
- Console
	- New console PHP script allow you to create Apache/nGinx basic
	authentication (more features will come)
- Dashboard
	- Last period jobs, stored bytes and stored files on take backup jobs
	- Jobs completed with errors are now displayed in last period job status
	widget
	- Backup job and client report are now available from top drop-down menu
	- New Clients jobs total widget display statistics for backup and restore
	jobs
	- New Weekly jobs statistics widget display stored bytes and files of each
	day of the week
- Jobs report
	- Filter jobs by type
	- Reset filter and options using the <Reset to defautl> button
- Pools and volumes report
	- Split into two specific reports page (pool and volumes)
- Volumes
	- Display an icon for each volumes which vary depending volume status
	- You can sort volumes by name, id, jobs and bytes from now
- Backup job report
	- Display only backup jobs (was showing restore or other jobs before)
	- You can now choose between 1 week, 2 weeks and 1 month for period
- Client report
	- You can now choose between 1 week, 2 weeks and 1 month for period
	- Fixed client details for MS Windows file daemons
- Documentation
	- Documentation got cleaned up, restructured and updated
- Development
	- Remove static php class with PDO
	- Large amount of code improvements and cleanup
	- Use Composer autoloader
	- Improve application exception handling
	- Add CodeClimate for CI
	- DateTimePicker updated to version 4.17.47
	- Bootstrap updated to version 3.3.7
	- FontAwesome updated to version 4.7.0
	- Smarty-Gettext updated to version 1.2.0

### Fixed bug(s)

- 0000177 [bug-html] graphing overlap on index.php
- 0000196 [bug] after install bacula web , the test page is OK but the index page returns errors
- 0000206 [bug-php] Expired and period fields do not use custom date format
- 0000212 [bug-php] Backup job report page should only display backup jobs
- 0000214 [bug-php] Last used volumes widget in Dashboard display unused volumes
- 0000224 [bug-php] PHP notice in client report for Windows clients

### New feature(s)

- 0000002 [feature] Use icon for volume status
- 0000005 [feature] Volumes sort order
- 0000088 [feature] View with Multiple Catalog
- 0000129 [feature] Allow disabling some types of backup jobs
- 0000155 [feature] Display Completed jobs with errors in Dashboard
- 0000173 [feature] Choose different period for Backup job report
- 0000181 [feature] Filter jobs by Type on Job Reports page
- 0000197 [feature] Add Client and Week size
- 0000199 [feature] Include a Kind of JobTotals
- 0000210 [feature] Add a "reset to default" option in Jobs report page

### Translations

- New Catalan, Belorusian and Polish languages
- All languages have been updated from Transifex

### Documentation

- 0000198 [documentation] Pb with symlink in .tgz ??
- 0000208 [documentation] Error assinging owner to config.php

## Bacula-Web 7.4.0 (May 7th 2017)

### Changelog

- General: Minimum PHP version is now 5.6, the test page have been updated accordingly
- Jobs report: Include more Levels InitCatalog=V, Catalog=C, VolumeToCatalog=O, DiskToCatalog=d and Data=A (wanderleihuttel)
- Jobs report: Align job name on the left (wanderleihuttel)
- Jobs report: Add Job scheduled time (sgargel)
- Backup job report: Fix "Backup job name" to "Job name" as there's not only backup jobs in Bacula

### Fixed bugs

- 0000203 [bug-php] Ubuntu PHP version string problem

### New features

- 0000176 [feature] Custom date time format in config.php
- 0000186 [feature] Add column "InChanger" in Volumes/Pool
- 0000204 [enhancement] Provide SHA checksum for Bacula-Web compressed archive
- 0000205 [feature] Filtering by pool on jobs report page

### Translations

none

## Bacula-Web 7.3.0 (December 4th 2016)

Changelog

- General: Improve responsive design for all pages (thanks to feldsam)
- Pool and volumes: Add accordion for each pool
- Pool and volumes: Used and Full volume status shows expiration date (thanks to wanderleihuttel)

### Fixed bugs

none

### Translations

- Added Russian translation (thanks to sayanvd) 
- Added Chinese translation (thanks to cxl nh)
- Added Norvegian translation (thanks to Georg Herland)
- Updated Spanish translation (thanks to Robert López)

## Bacula-Web 7.2.0 (June 28th 2016)

### Changelog

- General: The catalog dropdown is now displayed even if you have only one catalog defined 
	   I tought it would be much better to always see on which bacula instance your are monitoring
- General: Dashboard link have been removed from reports dropdown menu in header
- General: Bootstrap upgraded to version 3.3.6
- General: Bacula-Web will not die even if there's no data to display in graphs (see Mantis #00180 below)
- Dashboard: Improved dashboard layout by moving catalog statistics on top of the page
- Test page: As of now, you should run at least PHP version 5.4 to have Bacula-Web running smoothly 

### Fixed bugs

- 0000180 [bug-php] DrawGraph No data array error in http logs
- 0000187 [bug-php] Total of bytes per pool problem in Pool and volumes report
- 0000190 [bug-php] Some backup jobs in Jobs report page shows a duration of 0 second

### New features

- 0000142 [feature] Filter Job Report by Backup Level
- 0000192 [feature] Add filter by Start/End Time in Jobs report
 
### Translations

- As of Bacula-Web 7.1.1, all language translations are hosted on Tranifex.com)
  If you want to help translating in your language, please follow Bacula-Web documentation

## Bacula-Web 7.1.0 (April 4th 2016)

### Changelog

- Dashboard: Fixed problem with jobs running since more than 24 hours
- Pools and volumes: 
  - Total of bytes used now displayed for each pool
  - Display slot number of volumes (if using a library)
- General: 
  - PHP 5.6 and higher version compatibility improvements
  - Many code fixes and improvements
- Pools and volumes report: Volumes table now display slot number
- Jobs report: you can filter jobs with status "Terminated with errors"
- Translation: Portuguese brazilian have been updated and added Japanese

### Fixed bugs

- 0000170 [bug] Warning: Division by zero in jobs.php
- 0000171 [bug] Running and Waiting jobs older than 24 hours are not visible in Dashboard
- 0000172 [bug-php] Sometimes we see negative 0.00 compression value in job view
- 0000174 [bug-php] PHP issues with non-static methods call
- 0000185 [bug-php] Wrong backup job speed in Backup Job report

### New features

- 0000157 [feature] Add missing job status filter (Terminated with errors) in Jobs report page
- 0000159 [feature] Volume report slot in tape robot
- 0000175 [feature] Display total of Bytes for each Pool

### Translations

- Added Japanese translation (thanks to Ken Sawada)
- 0000169 [translation] Updated Portugues Brazilian translation (thanks to Brivaldo Junior)

## Bacula-Web 7.0.3 (March 26th 2015)

### Changelog

- General: Fixed several security issues and bugs in the code
- General: Upgraded Twitter Bootstrap framework from 3.3.1 to version 3.3.4

### Fixed bugs

- 0000162 [bug] Client jobs filter does not work in Jobs report page
- 0000163 [bug-php] Dashboard: Selected period in period selector do not reflect user's choice
- 0000165 [bug] Problem with the catalog selector
- 0000166 [bug] joblogs.php is showing wrong jobid
- 0000167 [bug] Problem enabling Smarty template cache
- 0000168 [bug-php] Division by zero warning in Jobs report page (bacula-dev)

### New features

none

### Translations

none

## Bacula-Web 7.0.2 (January 22nd 2015)

### Changelog

- General: Catalog selector now display current catalog label
- General: Web UI look and feel improvements
- General: Improved PHP code security and best practices compliance (PSR2)
- General: Improve support of PHP version 5.5 (see #000147)
- General: Pie graphs without data will display a message like "There's no data to display" (see #000137)
- General: With version 7.0.1, graphs appear randomly. This has been fixed by delaying graphs display (thanks to cpasqualini)
- General: Breadcrumb navigation bar design has been improved
- Jobs report: Jobs icon are now colourized depending on the job status
- Translations: Update messages.po file for all languages
- Requirements: Minimal supported PHP version have been raised to >= 5.3

### Fixed bugs

- 0000100 [bug-pgsql] Wrong SQL to make 'Pools and volumes status' graph
- 0000137 [bug-php] Pie graph empty if there's no value
- 0000140 [bug-php] Fix and improve breadcrumb navigation
- 0000145 [bug/design] Catalog selector should display current catalog name
- 0000146 [bug] Graphs sometimes disappear on Firefox 33.0
- 0000147 [bug-php] Improve support for PHP version >= 5.5
- 0000150 [bug-php] Improve check of $_GET value for joblogs.php
- 0000151 [bug-php] Period string not translated in Dashboard
- 0000153 [bug-php] Missing file include in client report page
- 0000154 [bug-php] Potential bug in constant definition (global.inc.php)
- 0000156 [bug-php] Icon not visible for completed jobs with errors in Jobs report page
- 0000158 [bug-php] Catalog id stored in user session should not take precedence over default catalog
- 0000160 [bug-html] Graphs loaded randomly (issue 146 not yet fixed)

### New features

none

### Translations

- Updated French translations

## Bacula-Web 7.0.1 (November 23rd 2014)

### Changelog

- General: Upgrade Twitter Bootstrap from version 3.2.0 to 3.3.1
- General: Fix html code issue in some templates using Bootlint (https://github.com/twbs/bootlint)

### Fixed bugs

- 0000143 [documentation] bacula-web 7.0.0 and the latest version of wget
- 0000144 [bug-php] Backup job report - php errors in logs
- 0000146 [bug] Graphs sometimes disappear on Firefox 33.0

### New features

none

## Bacula-Web 7.0.0 (November 19th 2014)

### Changelog

- General: Several code improvements
- General: Web UI have been reworked using Twitter Bootstrap, JQuery and FontAwesome
- Jobs report: Fix link problem with jobs name containing space(s)
- Updated Smarty to version 2.6.28
- Updated PHPLot from version 5.8.0 to 6.1.0

### Fixed bugs

- 0000083 [bug] Bacula-web dashboard queries to *sql does not take into account timezone differences
- 0000138 [bug-php] Display job compression rate only for Backup jobs
- 0000132 [translation] Update pt_BR translation (thanks to Brivaldo Junior)
- 0000133 [bug-php] Division by zero after update to 6.0.1
- 0000091 [design] Visibility long volume-names
- 0000077 [design] "Pools and volumes status" pie and legend overlap with long names

### New features

- 0000135 [feature] Add Stored Files graph in Dashboard
- 0000134 [feature] Add compression rate into Jobs report page

## Bacula-Web 6.0.1 (July 29th 2014)

### Changelog

- Updated copyright header in all PHP scripts

### Fixed bugs / New features

- 0000130 [bug-php] Y axis title is missing on some bar graphs
- 0000113 [bug-php] Bar plot graphs display data based on 1GB only
- 0000121 [feature] Job log not using <br> for EOL
- 0000123 [bug-html] Job summary shows as single line in joblogs.php
- 0000126 [bug-php] Wrong value for Total files and Total bytes in Dashboard
- 0000127 [security-issue] XSS and SQL injection security issue
- 0000116 [bug-php] division by zero after update to 6.0.0
- 0000111 [bug-php] Problem with Jobs Report page and PHP prior 5.2 - Call to undefined function date_parse()
- 0000120 [feature] Display Jobs speed and compression in Backup Job report page
- 0000108 [bug-mysql] Error with catalog size with MySQL 5.5.32 (Ubuntu0.12.04.1) 
- 0000114 [feature] Order jobs by date in Jobs report pag
- 0000115 [design] Add a "reset" in Jobs report option box 

## Bacula-Web 6.0.0 (November 11th 2013)

### Changelog

- General php code improvements
- Renamed docs folder to DOCS - General CSS code cleanup and improvements
- Fixed smarty-gettext version (using latest version, even if it's outdated)
- Job logs report: Added message "No log(s) for this job" in Job logs report page when logs are missing
- General: New Navigation "Back" link on top of each page (except Dashboard)
- Dashboard: Fixed label position for Pie chart in CGraph->Render() function
- General: Fixed problem for users having an javascript disabled (or noscript
- Dashboard: Implemented period selection in dashboard (last day, last week, last month, since beginning of time)

### Fixed bugs

- 0000069 [security-issue] Prevent to browse some directories
- 0000076 [design] Add noscript submit button where ever it does not exist (example provided)
- 0000089 [bug-mysql] Bypass database size information in Dashboard for MySQL version 4.x.x
- 0000098 [bug] gitignore included in tarball
- 0000099 [bug] Default graph colors hides legends
- 0000101 [bug-pgsql] Wrong SQL to make 'Last used volumes graph'
- 0000102 [bug-pgsql] error on get information from remote postgresql server 

### New features

- 0000043 [feature] Job Status Report for a specific client 
- 0000052 [feature] Database connection through unix socket
- 0000067 [feature] Dashboard date range selector
- 0000073 [feature] Widget "Last 24 hours status" should be switchable to LAST_WEEK and LAST_MONTH
- 0000106 [feature] Default jobs number settings in configuration
- 0000107 [feature] Jobs average speed in Jobs and backup job report

## Bacula-Web 5.2.13-1 (April 21st 2013)

### Changelog

- Dashboard: Fixed bug with MySQL database size sql query

### Fixed bugs

- 0000097 [bug] Trouble when doing a fresh install
- 0000096 [bug-mysql] SQLSTATE[22003]: Numeric value out of range: 1690 BIGINT UNSIGNED value is out of range
- 0000095 [bug-mysql] Obtaining db size fails for large catalogs databases

## Bacula-Web 5.2.13 (April 1st 2013)

### Changelog

- General: code cleanup and improvement
- General: improved header look and feel (cleaned up html and css)
- General: ability to use home icon in the header to show the dashboard
- General: improved exception handling by showing exception trace
- General: implemented PHP class autload
- General: browser title display the current page name from now
- Test page: added php timezone check
- Pools and volumes report page: fixed html code for empty pools
- Test page: fixed a bug with database connection status (was showing wrong connection status)
- Jobs report page: moved display options box on the right

### Fixed bugs

- 0000086 [bug] Wrong waiting jobs count in Dashboard widgets
- 0000075 [bug-pgsql] Special Character in db_name results in "database does not exist"
- 0000087 [bug-mysql] Issue accessing dashboard page with MySQL 5.0.32-7
- 0000093 [bug-mysql] Database error
- 0000090 [bug-html] Remove the top white bar on the main page
- 0000079 [bug] Slow graphs generation

## Bacula-Web 5.2.12 (January 24th 2013)

### Changelog

- Some internal php code cleanup
- Cleaned up CSS code for header
- Dashboard: Fixed items order for Pools and volumes status widget
- Job report page: Display "No job(s) to display" when there's no jobs result instead of an empty table
- Job logs page: Fixed bug with "odd and even" row in job logs table
- General: Header look and feel improvements
- General: Updated bug tracker url in header

### Fixed bugs

- 0000066 [bug-pgsql] new bacula-web install reports SQL error
- 0000068 [bug-pgsql] pgsql bug with Pools graph in dashboard 
- 0000085 [bug-pgsql] pgsql bug with Pools and volumes widget when more than 9 Pools
- 0000082 [bug-php] Wrong Clients information in Catalog statistics widget
- 0000081 [bug-php] Wrong FileSets information in Catalog statistics widget
- 0000078 [bug-php] Last 24 hour summary is reporting the runnign jobs incorrectly
- 0000080 [bug] Wrong jobs count value in last used volumes widget (Dashboard)

### Translations

- 0000084 [translation] Dutch translation updated

## Bacula-Web 5.2.11 (December 15th 2012)

### Changelog

- improved look and feel in general
- improved look and feel of grids in all pages
- code improvements with PHP PDO classes and starting kind of MVC
- code improvements for graph rendering php class
- updated application exception messages 
- cleaned up and splitted CSS code
- Dashboard: fixed legend items order for Pools and volumes status widget
- Pools and volumes report: highlighted volume name column in Pools and volumes report page
- Jobs report: fixed bug with SQLite for waiting jobs elapsed time
- Jobs report: improved and cleaned up design
- Dashboard: added last 24 hours running jobs information
- Dashboard: merged Transfered bytes and Transferted files into the same cell
- enhanced look and feel of the header
- upgraded Smarty template engine to version 2.6.27
- Dashboard: added volumes total used disk space
- Dashboard: merged pools and volumes cells
- Pools and volumes report page: empty Pools would not be displayed if the option $hide_empty_pools in the config file is set to true
- Jobs report: now you can sort job list by different criteria (JobId, Pool name, Job name, Job bytes and Job files) in Jobs report page
- Jobs report: displayed jobs / total jobs has been fixed
- Dashboard: total of defined jobs and filesets are now displayed in a widget 
- Dashboard: total of volumes is now displayed in a Dashboard widget 
- Jobs report: from now, a "No job(s) to display" message is displayed if there's no job result

### Fixed bugs

- 0000047 [bug-php] index.php is blank
- 0000050 [bug-php] Unable to access to the interface "Blank page"
- 0000068 [bug-pgsql] pgsql bug with Pools graph in dashboard
- 0000066 [bug-pgsql] ]new bacula-web install reports SQL error
- 0000071 [design] Missleading colors in dashboard
- 0000063 [bug-sqlite] Jobs list - Elapsed time incorrect for running jobs

### New features

- 0000044 [feature] Job status report - Sort job list by different column
- 0000039 [feature] multiple catalogs: shows all pools from all catalogs
- 0000003 [feature] Display total for stored volumes

## Bacula-Web 5.2.10 (August 9th 2012)

- upgraded Smarty gettext plugin to version 0.9.1
- many CSS code improvements
- SQLite database support improvements
- fixed database size with SQLite in dashboard
- added SQLite PDO support in test page
- test page display template cache full path
- files structure have been improved and cleaned up
- many internal code improvements
- fixed bugy CSS with Internet Explorer
- improved design of dashboard
- in job reports page, restore jobs were displaying empty value for pool, replaced now by N/A
- upgraded PHPLot to version 5.8.0
- portuguese brazil translation have been added (Thanks to Brivaldo Junior)
- new option in configuration file [show_inactive_clients]
- numbers are now formated regarding choosen language in the configuration
- documentation have been moved into /docs folder

### Fixed bugs / New features

- 0000045 [feature] Backup job - ability to see logs
- 0000057 [bug-html] Bacula-web does not display a version string in the web interface
- 0000042 [feature] Dashboard can not show all graph legends
- 0000062 [bug-php] SQLite connection issue - invalid data source name
- 0000061 [bug-html] Design issue with Internet explorer 8
- 0000055 [translation] Upgraded pt_BR translation
- 0000038 [feature] With multiple catalogs every client is shown in Reports drop down
- 0000033 [bug] Client report stops at RestoreJob
- 0000059 [bug-html] Large numbers showing apostrophes instead of commas for thousands separator
- 0000058 [bug-html] client-report.php is reporting deprecated notice errors about split()

## Bacula-Web 5.2.6 (March 30th 2012)

### Changelog

- Updated french translation (thanks to Guillaume Delacour)
- Fixed client sort order in Client report form (dashboard)
- Updated links in header to point to the new website (About and Bugs)
- Fixed charset (utf8) in template header
- Added PHP session support check in test page
- Fixed client architecture field in Client report page
- Improved exceptions and error handling
- Fixed bug with last used volume in dashboard (not used volumes not displayed)
- PHP code improvement

### Fixed bugs / New features

- 0000049 [bug] Port setting for database not picked up
- 0000048 [translation] Updated French translation

## Bacula-Web 5.2.2 (December 11th 2011)

### Changelog

- New layout for dashboard
- Updated last day status with canceled jobs (dashboard)
- Pool column in last used volumes widget
- Improved database support (fixed bugs and better exception handling)
- Updated translations for all languages
- Exception handling when config file contain bad parameters or missing database connection
- Database and application exception handling
- Upgraded PHPLot to latest version (version 5.5.0)

### Fixed bugs / New features

- 0000036 [bug] No localisation (bacula-dev) - resolved.
- 0000010 [bug] Improve postgreSQL (bacula-dev) - resolved.

## Bacula-Web 5.1.0 (July 25th 2010)

### Changelog

- Improved dashboard statistics
- Improved database support
- Multi-catalog support enhancements
- Support for PHP version 4 removed
- Cleaned up HTML and CSS code (more W3C compliant)
- New elements checked in test page such as PHP version, template cache folder permissions, PHP PDO support, etc.
- New Client backup report page
- New Pools and volumes page
- Fixed security issue reported by Leonardo Rota Botelho

### Fixed bugs / New features

- 0000023 [design] Problem with graph in the General report with more than 12 Backup jobs (bacula-dev) - resolved
- 0000034 [bug-php] Useless PEAR:DB include in global.inc.php file (bacula-dev) - resolved
- 0000030 [bug-html] Error.gif instead of Images (bacula-dev) - resolved
- 0000029 [bug-pgsql] Unable to get volume number from catalog (bacula-dev) - resolved
- 0000031 [bug-php] Big Red X in right side when Full mode is enabled (bacula-dev) - resolved
- 0000004 [bug] Make w3c standard html code (bacula-dev) - resolved
- 0000027 [bug-php] PHP Errors (bacula-dev) - resolved
- 0000026 [bug-pgsql] PostgreSQL error (bacula-dev) - resolved
- 0000025 [bug] Images not appearing correctly in latest version (bacula-dev) - resolved
- 0000024 [design] Improve design of Bacula-Web when there's a lot of volumes (bacula-dev) - resolved
- 0000019 [bug] Unreadable dates in graph (bacula-dev) - resolved
- 0000012 [bug] Wrong spelling in popup (bacula-dev) - resolved
- 0000011 [bug] Wrong size of database with postgreSQL (bacula-dev) - resolved
- 0000006 [feature] Scale Down bytes backed up to Kbytes, Mbytes or Gbytes (bacula-dev) - resolved
- 0000022 [bug-php] List of jobs are no more display as expected (bacula-dev) - resolved
- 0000007 [feature] Use JPgraph instead of phplot (bacula-dev) - closed

## Bacula-Web 5.0.3 (November 2nd 2010)

### Changelog

- Changed content of README file
- Added new email address in CONTACT file
- Removed TODO file
- Removed changeLog file
- Moved images folder into style directory
- Removed var_dump used for debug from report.php
- Removed fsize_format tag from report.tpl
- Improved volumes list
- Changed css classes for header and boxes
- Removed fzise_format and new function in classes.inc
- New function ByteToSize() in classe Bweb classe
- Adapted path for Smarty lib in paths.php
- Upgraded Smarty to latest version (Smarty 2.6.26)
- Fixed html and css issue in report page - Fixed in report.tpl and report_select.tpl
- Fixed html issue in index.tpl - Replaced all & by &amp; in phplot graph
- Improved css code in volumes.tpl
- Improved html code in volumes.tpl
- Fixed Smarty typo in volumes.tpl
- Fixed Overlib bug in index.tpl
- Fixed typo in graph in classes.inc
- Improved design and fixed html/css issue
- Changed header.tpl design and links
- Changed CSS file declaration in full_popup.tpl
- Fixed typo in Javascript declaration
- Improved test page
- Fixed all php short tag
- Fixed some html and css issue
- Changed header.tpl content
- Changed css file location in report.tpl
- Changed css file location in style/default.css
- Removed title variable from config file
- New style folder
- Ignore Smarty cache files

### Fixed bugs / New features

- none

## Bacula-Web 1.3 (January 26th 2006)

### Changelog

- 0000019,0000021 Fixed psql querys.
- Fixed XTicks (Graphs).

### Fixed bugs / New features

- (0000019,0000021) Fixed psql querys.

## Bacula-Web 1.2 (October 6th 2005)

### Changelog

- Human readable Y-axis of graphs
- Updated French translation
- Initial support for PostgreSQL (thanks to Dan Langille)
- Upgrade Phplot to 5.0rc2 version.
- Added templates_c to cvs repository.
- Added German translation
- Fix mysql database size (reported by Roland Arendes)
- Added JobID column in report template. (thanks to Stephan Ebeit)
- Upgraded Smarty to 2.6.10 version.
- Added multicatalog support

### Fixed bugs / New features

- none

## Bacula-Web 1.1 (October 25th 2004)

### Changelog

- Add array_fill function (as Mikael suggested)
- Fix url encode of links. (reported by Phil Stracchino)
- Upgraded Smarty to 2.6.6 version.
- Add French translation

### Fixed bugs / New features

- none

## Bacula-Web 1.0 (August 4th 2004)

### Changelog

- Add Italian translation
- Change some colors of graphs. More clear now (I hope).

### Fixed bugs / New features

- (0000012) Fix, don't load config in report.php
- (0000015) Fix, error date in When expire? field.

## Bacula-Web 1.0 Beta2 (June 14th 2004)

### Changelog

- none

### Fixed bugs / New features

- (0000009) Fix incorrect date of â€œselect report.
- (0000010) Fix incorrect symbolic link (Add paths.php).
- (0000008) Fix elapsed time of execution of jobs more than 24h in Lite mode.

## Bacula-Web 1.0 (June 11th 2004)

### Changelog

- Check connection error (reported by Thomas Contamine)
- Register_globals on/off compatible (reported by Thomas Contamine)
- Fix bug in graph type=69. The data now is correct
- Fix config system
- Very internal fixes
- Unordered List ItemAdd at last report

### Fixed bugs / New features

- none
