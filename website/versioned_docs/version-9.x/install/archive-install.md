# Install using archive

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';

Download the archive

```shell
$ curl -O -L https://github.com/bacula-web/bacula-web/releases/download/v9.8.0/bacula-web-v9.8.0.tgz
```

::::info
Since Bacula-Web version 9.8.0, pre-installed archive and signature files are available in [GitHub releases](https://github.com/bacula-web/bacula-web/releases).
::::

## Verify archive signature (optional)

Both SHA 256 and 512 signature files are available from Bacula-Web project release page

I'd strongly suggest to verify the integrity of the compressed archive you downloaded before proceeding to the installation.

SHA256 https://github.com/bacula-web/bacula-web/releases/download/v9.8.0/bacula-web-sha256.txt

SHA512 https://github.com/bacula-web/bacula-web/releases/download/v9.8.0/bacula-web-sha512.txt

Download the SHA sum file using the link above (adapt the version, the links are just examples).

Once downloaded, run one the command below from the same path where you've downloaded the `bacula-web-<version>.tgz`

```shell
$ cat sha256sum.txt | sha256sum -c
or
$ cat sha512sum.txt | sha512sum -c
```

Both command should output something like below

```shell
bacula-web-v9.8.0.tgz: OK
```

## Decompress the archive

```shell
$ tar -xvf bacula-web-v9.8.0.tgz
```

## Fix files/folders ownership and permissions

Move the folder in the web server folder

<Tabs>
  <TabItem value="rpm" label="For Red Hat / Centos / Fedora" default>
```shell
$ sudo mv -v bacula-web /var/www/html/
```
  </TabItem>
  <TabItem value="deb" label="For Debian / Ubuntu">
```shell
$ sudo mv -v bacula-web /var/www/
```
  </TabItem>
</Tabs>

Follow instructions to [set permissions and ownership](../admin-guides/permissions-and-ownership.mdx) in your Bacula-Web folder.

Once you're done, please proceed to the [configuration](configure.md) of Bacula-Web.
