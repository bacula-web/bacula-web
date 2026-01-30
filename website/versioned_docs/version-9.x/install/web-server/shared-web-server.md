# Shared web server

You can install Bacula-Web on a shared web server.

For example, Bacula-Web can be available at

`https://www.domain.com/bacula-web/`

so you could also have others web app installed on the same server

- `https://www.domain.com/wordpress/`
- `https://www.domain.com/wiki/`

## Instructions

The following instructions use [https://www.domain.com/bacula-web/](https://www.domain.com/bacula-web/) for the url, please
change to fits your own setup.

- Upload and uncompress the Bacula-Web compressed archive on your web server.

  The web server directory could vary depending on the OS you're using but it's generally under `/var/www` for most Linux distributions, 
  snf `/usr/local/www` on *BSD.
- Make sure that the [ownership and permissions](../../admin-guides/permissions-and-ownership.mdx) have been set correctly

### Nginx

Nginx web server can be configured as below

```nginx
server {
    ...
    server_name www.domain.com;
    ...
    location /bacula-web/ {
        try_files $uri /index.php$is_args$args;
    }
    ...
```

### Apache

Configure the default Apache virtualhost as below

```apacheconf
<VirtualHost *:80>
	ServerName www.domain.com
	
	# config truncated
	...
	
	Alias /bacula-web /var/www/bacula-web/public
    <Directory /var/www/bacula-web>
  	  Options Indexes FollowSymlinks
      AllowOverride all
      Require all granted
    </Directory>
</VirtualHost>
```
