# Using SELinux

To install Bacula-Web with SELinux enforced, please use instructions below

Check if SELinux is enabled

As root, run the command below to check how SELinux is configured on your system


```shell
# getenforce
```

If you get a status

```shell
Enforced
```

and Bacula-Web doesn't work, first of all, check Selinux log file

```shell
# tail /var/log/audit/audit.log

If you see entries related to Bacula-Web script files ....
```

```
...
type=AVC msg=audit(1418826191.935:69): avc:  denied  { relabelto } for  pid=1595 comm="chcon" name="%%F7^F7F^F7F34188%%header.tpl.php" dev=dm-0 ino=403104 scontext=unconfined_u:unconfined_r:unconfined_t:s0-s0:c0.c1023 tcontext=system_u:object_r:removable_device_t:s0 tclass=file
type=SYSCALL msg=audit(1418826191.935:69): arch=40000003 syscall=227 success=no exit=-13 a0=bfb2700c a1=383629 a2=99b1bd8 a3=28 items=0 ppid=1319 pid=1595 auid=0 uid=0 gid=0 euid=0 suid=0 fsuid=0 egid=0 sgid=0 fsgid=0 tty=pts0 ses=1 comm="chcon" exe="/usr/bin/chcon" subj=unconfined_u:unconfined_r:unconfined_t:s0-s0:c0.c1023 key=(null)
...
```

## Restore SELinux security context
--------------------------------

This command should fix the problem

```shell
# restorecon -R -v /var/www/html/bacula-web
```

For users using Centos/Red Hat version 7, use the command below to set the right security context on the cache folder

```shell
# chcon -R -t httpd_sys_rw_content_t /var/www/html/bacula-web/application/view/cache
```

If you are running Apache web server, make sure the PHP session path (`/var/lib/php/session`) have the correct SELinux context.

To check it, run this command on your server

```shell
    php -i | grep session.save_path
```

and if needed, restore the correct SELinux context

```shell
# restorecon -Rv /var/lib/php/session
```

::::tip
*Should you use SELinux on your server ?*

My answer is **YES**, For security purpose, I would strongly encourage people to keep SELinux enabled.
::::

## Resources

If you need more information about SELinux and security, use the links below

* [Centos wiki](http://wiki.centos.org/HowTos/SELinux)
* [Fedora project wiki](http://fedoraproject.org/wiki/SELinux)
* [Red Hat - Working with SELinux](https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_Linux/6/html/Security-Enhanced_Linux/chap-Security-Enhanced_Linux-Working_with_SELinux.html)
