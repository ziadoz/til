FROM php:8.4-cli

# Copy MySQL ARM64 binaries from official image...
COPY --from=mysql:8.4.4 /usr/bin/mysql /usr/local/bin/mysql
COPY --from=mysql:8.4.4 /usr/bin/mysqldump /usr/local/bin/mysqldump

# Install PHP extensions as usual...
RUN apt-get update && apt-get install -y libzip-dev && docker-php-ext-install zip
