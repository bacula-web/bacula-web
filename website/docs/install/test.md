# Test

After installing/upgrading and configuring Bacula-Web, just ensure that Bacula-Web will work fine.

## Using the test page

A test page exist for this purpose that check the following items

* required package are successfully installed
* Twig cache folder good permissions
* application database back-end good permissions
* php modules are installed and properly configured

To test your installation of Bacula-Web, follow this link

[http://yourserveroripaddress/bacula-web/test](http://yourserveroripaddress/bacula-web/test)

You should got the same result as shown in the screenshot below

![Bacula-Web - Test page](../assets/bacula-web-test-page.jpg)

## Using Bacula-Web console

Bacula-Web console is a PHP script which can be run from the command line only.

This script verifies if you've installed all required dependencies and make sure everything has been configured correctly
to run Bacula-Web.

Open a shell command prompt and move to Bacula-Web installation folder

```shell
$ cd /var/www/html/bacula-web
```

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';

<Tabs>
  <TabItem value="rpm" label="On Red Hat / Centos / Fedora" default>
```shell
$ sudo -u apache php bwc check
```
  </TabItem>
  <TabItem value="deb" label="On Debian / Ubuntu">
```shell
$ sudo -u www-data php bwc check
```
  </TabItem>
</Tabs>

:::info
Bacula-Web console is available since version 8.1.0
:::
