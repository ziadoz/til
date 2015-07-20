# Update Ubuntu
sudo apt-get install build-essential
sudo apt-get update
sudo apt-get upgrade

# Version Control
sudo apt-get install git mercurial subversion

# Mobile Shell (MOSH)
# See: http://mosh.mit.edu/
sudo apt-get install python-software-properties software-properties-common
sudo add-apt-repository ppa:keithw/mosh
sudo apt-get update
sudo apt-get install mosh

# MySQL
sudo apt-get install mysql-server

# Nginx
sudo apt-get install nginx
sudo service nginx start

# Nginx Mime Types
curl https://raw.github.com/h5bp/server-configs/master/nginx/mime.types | sudo tee /etc/nginx/mime.types

# Nginx Setup
# See: http://fideloper.com/ubuntu-12-04-lemp-nginx-setup
# See: https://github.com/h5bp/server-configs/tree/master/nginx
sudo nano /etc/nginx/nginx.conf
sudo service nginx restart

# PHP
# See: http://askubuntu.com/questions/56933/php-upgrades-from-5-3-2-to-5-3-6/265049#265049
sudo apt-get install python-software-properties software-properties-common
sudo add-apt-repository ppa:ondrej/php5
sudo apt-get update
sudo apt-get upgrade
sudo apt-get install php5-fpm php5-common
sudo apt-get install php5-cli php5-imagick php5-mcrypt php5-mysqlnd php5-gd php5-intl php5-xdebug php5-curl

# PHP Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# PHP Setup
# See: https://www.digitalocean.com/community/articles/how-to-install-linux-nginx-mysql-php-lemp-stack-on-ubuntu-12-04
# See: http://fideloper.com/ubuntu-12-04-lemp-nginx-setup
sudo nano /etc/php5/fpm/php.ini
sudo service php5-fpm restart
sudo nano /etc/php5/fpm/conf.d/20-apc.ini
apc.enabled=1
apc.stat=1

# Ubuntu Security
# See: http://plusbryan.com/my-first-5-minutes-on-a-server-or-essential-security-for-linux-servers
sudo apt-get install ufw
sudo ufw allow 25000
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw allow 60001/udp