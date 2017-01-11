# Symlink web server virtualhost configutions into the sites-enabled directory.
# Usage: reloadvhosts /path/to/websites
#        reloadvhosts /path/to/websites *-live.conf
#        reloadvhosts /path/to/websites apache2
reloadvhosts() {
  local vhost_dir=${1:-/var/www/vhosts}
  local conf_name=${2:-*dev.conf}
  local web_server=${3:-nginx}

  sudo find /etc/$web_server/sites-enabled/ -type l -exec rm {} \;
  sudo find $vhost_dir -name $conf_name -exec ln -sf {} /etc/$web_server/sites-enabled/ \;
  sudo service $web_server restart
}