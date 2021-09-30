FROM php:8.0-fpm

# Copy Composer:
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install PHP extensions:
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions zip

# Install Google Chrome:
RUN apt-get update && \
    apt-get install -y gnupg2 && \
    curl -sS -o - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add && \
    echo "deb https://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list && \
    apt-get -y update && \
    apt-get -y install google-chrome-stable

# Install ChromeDriver:
RUN apt-get update && \
    apt-get install -y unzip && \
    curl -sS https://chromedriver.storage.googleapis.com/94.0.4606.61/chromedriver_linux64.zip -o chromedriver.zip && \
    unzip chromedriver.zip && \
    rm chromedriver.zip && \
    chmod +x chromedriver && \
    mv -f chromedriver /usr/local/bin/chromedriver

# Cleanup:
RUN apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
