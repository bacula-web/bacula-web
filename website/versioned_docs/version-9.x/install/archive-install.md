# Install using archive

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';

::::info
Since Bacula-Web version 9.8.0, pre-installed archive and signature files are available in [GitHub releases](https://github.com/bacula-web/bacula-web/releases).
::::

Download the archive

```shell
$ curl -O -L https://github.com/bacula-web/bacula-web/releases/download/v9.9.0/bacula-web-v9.9.0.tgz
```

## Verify archive signature (optional)

Both SHA-256 and SHA-512 signature files are available from the Bacula-Web project release page.

I strongly suggest checking the integrity of the compressed archive you downloaded before proceeding with the installation.

Download the sha signature file.

For the SHA-256 signature file
```shell
curl -LO https://github.com/bacula-web/bacula-web/releases/download/v9.9.0/bacula-web-sha256.txt 
```

For the SHA-512 signature file
```shell
curl -LO https://github.com/bacula-web/bacula-web/releases/download/v9.9.0/bacula-web-sha512.txt 
```

Once downloaded, run one of the commands below from the same path where you've downloaded the `bacula-web-<version>.tgz` file.

<Tabs>
    <TabItem value="Linux" label="On Linux" default>
    ```shell
    # using sha256
    sha256sum -c bacula-web-sha256.txt
    # using sha512
    sha512sum -c bacula-web-sha512.txt
    ```
    </TabItem>
    <TabItem value="macOS" label="On macOs" default>
    ```shell
    # using sha256
    shasum -a 256 -c bacula-web-sha256.txt
    # using sha512
    shasum -a 512 -c bacula-web-sha512.txt
    ```
    </TabItem>
</Tabs>

If the signature verification succeeds, the output should be as follows.

```shell
bacula-web-v9.9.0.tgz: OK
```

## Decompress the archive

```shell
$ tar -xvf bacula-web-v9.8.0.tgz
```

## Fix files/folders ownership and permissions

Move the folder in the web server folder

<Tabs>
  <TabItem value="rpm" label="For Red Hat / Centos / Fedora" default>
```shell title="Apache default directory is /var/www/html using Red Hat / Centos / Fedora, etc."
$ sudo mv -v bacula-web /var/www/html/
```
  </TabItem>
  <TabItem value="deb" label="For Debian / Ubuntu">
```shell title="Apache default directory is /var/www using Debian or Ubuntu"
$ sudo mv -v bacula-web /var/www/
```
  </TabItem>
</Tabs>

Follow instructions to [set permissions and ownership](../admin-guides/permissions-and-ownership.mdx) in your Bacula-Web folder.

Once you're done, please proceed to the [configuration](configure.md) of Bacula-Web.
