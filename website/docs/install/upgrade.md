# Upgrade

Upgrading Bacula-Web installation is very easy, you only need basic linux administration skills.

## Configuration backup

Before proceeding to the upgrade, make sure you do a copy of the config file and users database

* `<bacula-web path>/application/config/config.php`
* `<bacula-web path>/application/assets/protected/application.db`

Copy above files in your `$HOME` folder for example

```shell
$ sudo cp -pv <bacula-web path>/application/config/config.php $HOME/
$ sudo -pv <bacula-web path>/application/assets/protected/application.db $HOME/
```

### Check the requirements

Ensure that you meet all system requirements (more information are available in the [Requirements](requirements.md) page).

## Using Composer

Please use steps described below to upgrade Bacula-Web to latest stable version using Composer

Move to Apache root folder

**Red Hat / Centos / Fedora**

```shell
$ cd /var/www/html 
$ sudo mv -v bacula-web bacula-web-beforeupgrade
```

:::tip
The path might need to be adapted depending on your setup
:::

Get latest stable version of Bacula-Web

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';

<Tabs>
  <TabItem value="rpm" label="On Red Hat / Centos / Fedora" default>
```shell
$ sudo -u apache composer create-project bacula-web/bacula-web bacula-web @stable
```
  </TabItem>
  <TabItem value="deb" label="On Debian / Ubuntu">
```shell
$ sudo -u www-data composer create-project bacula-web/bacula-web bacula-web @stable
```
  </TabItem>
</Tabs>

Copy configuration and users database to new Bacula-Web folder

```shell
$ sudo cp -pv bacula-web-beforeupgrade/application/config/config.php bacula-web/application/config/
$ sudo cp -pv bacula-web-beforeupgrade/application/assets/protected/* bacula-web/application/assets/protected/
```

## Fix files ownership and permissions

Move the folder in the web server folder

```shell
$ sudo mv -v bacula-web /var/www/
```

Follow instructions to [set permissions and ownership](../admin-guides/permissions-and-ownership.mdx) in your Bacula-Web folder.

## Next step

Once the upgrade process is completed, you can [test your Bacula-Web installation](test)
