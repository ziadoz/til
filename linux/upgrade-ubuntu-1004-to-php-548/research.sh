# 1) Install via PPA.
# References:
# [1] Blog: http://www.upubuntu.com/2012/03/how-to-upgrade-install-php-540-under.html
sudo apt-get install python-software-properties
sudo add-apt-repository ppa:ondrej/php5
sudo apt-get update
sudo apt-get install php5


# 2) Compile from Source.
# Note: Change --with-libdir=lib64 to --with-libdir=lib if you are running 32bit.

# Install Dependencies:
sudo apt-get install libmysqlclient-dev mysql-client libcurl4-openssl-dev libgd2-xpm-dev libjpeg-dev libpng3-dev libxpm-dev libfreetype6-dev libt1-dev libmcrypt-dev libxslt1-dev bzip2 libbz2-dev libxml2-dev libevent-dev libltdl-dev libmagickwand-dev libmagickcore-dev imagemagick libreadline-dev libc-client-dev libsnmp-dev snmpd snmp libvpx-dev libxpm-dev libgmp3-dev libicu-dev libpspell-dev libtidy-dev libsasl2-dev

# Get PHP Source:
wget -O php-5.4.8.tar.bz2 http://www.php.net/get/php-5.4.8.tar.bz2/from/this/mirror
tar jxf php-5.4.8.tar.bz2
cd php-5.4.8/

# Compile.
./configure \
--prefix=/usr/local \
--with-libdir=lib64 \
--with-layout=PHP \
--with-pear= \
--with-apxs2=/usr/bin/apxs2 \
--enable-calendar \
--enable-bcmath \
--with-gmp= \
--enable-exif \
--with-mcrypt \
--with-mhash \
--with-zlib \
--with-bz2 \
--enable-zip \
--enable-ftp \
--enable-mbstring \
--with-iconv \
--enable-intl \
--with-icu-dir=/usr \
--with-gettext \
--with-pspell=/usr \
--enable-sockets \
--with-openssl \
--with-curl \
--with-curlwrappers \
--with-gd \
--enable-gd-native-ttf \
--with-jpeg-dir=/usr \
--with-png-dir=/usr \
--with-xpm-dir=/usr \
--with-vpx-dir=/usr \
--with-zlib-dir=/usr \
--with-freetype-dir=/usr \
--with-t1lib=/usr \
--with-libxml-dir=/usr \
--with-mysql=mysqlnd \
--with-mysqli=mysqlnd \
--with-pdo-mysql=mysqlnd \
--with-pgsql \
--enable-soap \
--with-xmlrpc \
--with-xsl \
--with-tidy \
--with-readline \
--enable-pcntl \
--enable-sysvsem \
--enable-sysvshm \
--enable-sysvmsg \
--enable-shmop \
--with-pcre-regex \
--with-imap \
--with-imap-ssl \
--with-snmp \
--with-kerberos \

make
make test
sudo make install

# Compile (without overwriting Apache Module).
./configure \
--prefix=/usr/local/php/5.4 \
--disable-debug \
--localstatedir=/usr/local/php/5.4/var \
--sysconfdir=/usr/local/php/5.4/etc \
--libexecdir=/usr/local/php/5.4/libexec \
--with-config-file-path=/usr/local/php/5.4/etc \
--with-config-file-scan-dir=/usr/local/php/5.4/etc/php5/conf.d \
--with-libdir=lib \
--with-layout=PHP \
--with-pear= \
--with-apxs2=/usr/bin/apxs2 \
--enable-calendar \
--enable-bcmath \
--with-gmp= \
--enable-exif \
--with-mcrypt \
--with-mhash \
--with-zlib \
--with-bz2 \
--enable-zip \
--enable-ftp \
--enable-mbstring \
--with-iconv \
--enable-intl \
--with-icu-dir=/usr \
--with-gettext \
--with-pspell=/usr \
--enable-sockets \
--with-openssl \
--with-curl \
--with-curlwrappers \
--with-gd \
--enable-gd-native-ttf \
--with-jpeg-dir=/usr \
--with-png-dir=/usr \
--with-xpm-dir=/usr \
--with-vpx-dir=/usr \
--with-zlib-dir=/usr \
--with-freetype-dir=/usr \
--with-t1lib=/usr \
--with-libxml-dir=/usr \
--with-mysql=mysqlnd \
--with-mysqli=mysqlnd \
--with-pdo-mysql=mysqlnd \
--with-pgsql \
--enable-soap \
--with-xmlrpc \
--with-xsl \
--with-tidy \
--with-readline \
--enable-pcntl \
--enable-sysvsem \
--enable-sysvshm \
--enable-sysvmsg \
--enable-shmop \
--with-pcre-regex \
--with-imap \
--with-imap-ssl \
--with-snmp \
--with-kerberos \

# Apache Module directory.
# Now you need to edit the Makefile and change the INSTALL_IT target.
# Take the code below and make the following replacements:
# 1) Replace <libexec> with the directory you specified with the --libexecdir switch above.
# 2) Replace <apxsdir> with the directory you specified with the --libexecdir switch above.
# 3) Replace <apachedir> with the directory where Apache is installed (I.e. /etc/apache2).
# Replace the old INSTALL_IT line of the Makefile with this new line.
# References:
# [1] Bug: https://bugs.php.net/bug.php?id=60812
# [2] Homebrew PHP Formula: https://github.com/josegonzalez/homebrew-php/blob/master/Formula/php54.rb
INSTALL_IT = $(mkinstalldirs) '<libexec>/apache2' && $(mkinstalldirs) '$(INSTALL_ROOT)<apachedir>' && <apxsdir> -S LIBEXECDIR='<libexec>/apache2' -S SYSCONFDIR='$(INSTALL_ROOT)<apachedir>' -i -a -n php5 libs/libphp5.so

# The new INSTALL_IT target would look like this (based upon the configuration above):
INSTALL_IT = $(mkinstalldirs) '/usr/local/php/5.4/libexec/apache2' && $(mkinstalldirs) '$(INSTALL_ROOT)/etc/apache2' && /usr/bin/apxs2 -S LIBEXECDIR='/usr/local/php/5.4/libexec/apache2' -S SYSCONFDIR='$(INSTALL_ROOT)/etc/apache2' -i -a -n php5 libs/libphp5.so

make
make test
sudo make install