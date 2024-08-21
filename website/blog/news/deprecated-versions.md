---
title: Deprecated versions
description: "Important information about deprecated versions"
date: '2018-06-01'
tags: ["news"]
---

## Deprecated versions

### Bacula-Web <= 8.4.2

Community users running Bacula-Web prior 8.4.2 are really encouraged to upgrade to at least version 8.4.2 as previous versions suffer of several Smarty PHP template engine security issues (see list below)

- [CVE-2021-26119](https://www.cvedetails.com/cve/CVE-2021-26119/)
- [CVE-2021-26120](https://www.cvedetails.com/cve/CVE-2021-26120/)

### Bacula-Web <= 8.0.0

Bacula-Web prior version 8.0.0 suffer several SQL injection and XSS vulnerabilities (see [CVE-2017-15367](https://www.cvedetails.com/cve/CVE-2017-15367/) and [CVE-2014-8295](https://www.cvedetails.com/cve/CVE-2014-8295/) for more details). 

Due to security issue mentioned above, I'd **strongly** recommend you to use at least Bacula-Web version 8.0.1.

A big thanks to *Gustavo Sorondo* for his help to fix those security issues.