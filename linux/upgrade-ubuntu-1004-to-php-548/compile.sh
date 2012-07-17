# Install Dependencies
# sudo apt-get install build-essential
# sudo apt-get build-dep php5
sudo apt-get install libmysqlclient-dev mysql-client libcurl4-openssl-dev libgd2-xpm-dev libjpeg-dev libpng3-dev libxpm-dev libfreetype6-dev libt1-dev libmcrypt-dev libxslt1-dev bzip2 libbz2-dev libxml2-dev libevent-dev libltdl-dev libmagickwand-dev libmagickcore-dev imagemagick libreadline-dev libc-client-dev libsnmp-dev snmpd snmp libvpx-dev libxpm-dev libgmp3-dev libicu-dev libpspell-dev libtidy-dev freetds-dev unixodbc-dev librecode-dev libglib2.0-dev libsasl2-dev libgeoip-dev imagemagick libmagickcore-dev libmagickwand-dev

# Stop Apache
sudo service apache2 stop

# Cleanup Packages
sudo apt-get autoremove

# Remove Ubuntu PHP Packages
sudo apt-get remove php5 php5-cli php5-common php5-curl php5-dev php5-gd php5-geoip php5-imagick php5-intl php5-mcrypt php5-mysql php5-xdebug php5-xsl

# Remove PECL Packages
sudo pecl uninstall apc
sudo pecl uninstall geoip
sudo pecl uninstall xdebug

# Get PHP 5.4.8 Source
wget -O php-5.4.8.tar.bz2 http://www.php.net/get/php-5.4.8.tar.bz2/from/this/mirror
tar jxf php-5.4.8.tar.bz2
cd php-5.4.8/

# Configure PHP Source
# Note: Use `php-config` utility to determine your existing PHP configure options.
./configure \
--prefix=/usr \
--with-apxs2=/usr/bin/apxs2 \
--with-config-file-path=/etc/php5/apache2 \
--with-config-file-scan-dir=/etc/php5/conf.d \
--build=x86_64-linux-gnu \
--host=x86_64-linux-gnu \
--sysconfdir=/etc \
--mandir=/usr/share/man \
--disable-debug \
--with-regex=php \
--disable-rpath \
--disable-static \
--with-pic \
--with-layout=GNU \
--with-pear=/usr/share/php \
--enable-calendar \
--enable-sysvsem \
--enable-sysvshm \
--enable-sysvmsg \
--enable-bcmath \
--with-bz2 \
--enable-ctype \
--with-db4 \
--without-gdbm \
--with-iconv \
--enable-exif \
--enable-ftp \
--with-gettext \
--enable-mbstring \
--with-pcre-regex=/usr \
--enable-shmop \
--enable-sockets \
--enable-wddx \
--with-libxml-dir=/usr \
--with-zlib \
--with-kerberos=/usr \
--with-openssl=/usr \
--enable-soap \
--enable-zip \
--with-mhash=yes \
--with-exec-dir=/usr/lib/php5/libexec \
--with-system-tzdata \
--without-mm \
--with-curl=shared,/usr \
--with-enchant=shared,/usr \
--with-zlib-dir=/usr \
--with-gd \
--enable-gd-native-ttf \
--with-gmp=shared,/usr \
--with-jpeg-dir=shared,/usr \
--with-xpm-dir=shared,/usr/X11R6 \
--with-png-dir=shared,/usr \
--with-freetype-dir=shared,/usr \
--enable-intl=shared \
--with-ttf=shared,/usr \
--with-t1lib=shared,/usr \
--with-ldap=shared,/usr \
--with-ldap-sasl=/usr \
--with-mysql=shared,/usr \
--with-mysqli=shared,/usr/bin/mysql_config \
--with-pspell=shared,/usr \
--with-unixODBC=shared,/usr \
--with-recode=shared,/usr \
--with-xsl=shared,/usr \
--with-snmp=shared,/usr \
--with-sqlite=shared,/usr \
--with-sqlite3=shared,/usr \
--with-mssql=shared,/usr \
--with-tidy=shared,/usr \
--with-xmlrpc=shared \
--with-pgsql=shared,/usr \
--enable-fpm \
--with-mcrypt \
--enable-pdo=shared,/usr \
--with-pdo-mysql=shared,/usr \
--with-pdo-sqlite=shared,/usr \
--with-pdo-pgsql=shared,/usr \
--with-curlwrappers \
--enable-json \
--enable-shared \
--enable-mysqlnd=shared \

# Install PHP
make
make test
sudo make install

# Update PEAR and Install PECL Packages
sudo pear update-channels
sudo pecl install apc 
sudo pecl install geopip
sudo pecl install xdebug

# Install Imagick RC2
wget http://pecl.php.net/get/imagick-3.1.0RC2.tgz
tar xvfz imagick-3.1.0RC2.tgz
cd imagick-3.1.0RC2
phpize
./configure --prefix=/usr/lib --with-bzlib=yes --with-fontconfig=yes --with-freetype=yes --with-gslib=yes --with-gvc=yes --with-jpeg=yes --with-jp2=yes --with-png=yes --with-tiff=yes
make
sudo make install

# Install XDebug 2.2.1
wget http://xdebug.org/files/xdebug-2.2.1.tgz
tar xvfz xdebug-2.2.1.tgz
cd xdebug-2.2.1
phpize
./configure --enable-xdebug
make
sudo make install

# Update PHP INI Extensions
# Use `php-m` to see a list of active modules.
sudo rm /etc/php5/conf.d/mcrypt.ini
sudo rm /etc/php5/conf.d/gd.ini
sudo echo 'extension=imagick.so' | sudo tee /etc/php5/conf.d/xdebug.ini
sudo echo 'zend_extension_ts=xdebug.so' | sudo tee /etc/php5/conf.d/xdebug.ini

# Start Apache
sudo service apache2 start