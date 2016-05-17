# config valid only for current version of Capistrano
#lock '3.4.1'

set :application, 'bacula_web'
set :repo_url, 'git@github.com:sakrow/bacula-web.git'

#set :deploy_to, '/var/www/viajarporescocia.com/contenido/themes/smarty-child'
set :deploy_to, '/var/www/bacula.blackslot.com/'

set :keep_releases, 5

set :linked_files,           ['application/config/config.php']