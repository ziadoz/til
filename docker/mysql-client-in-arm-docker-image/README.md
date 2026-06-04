# Using the MySQL Client in an ARM Docker Image

Installing `mysql-client` from a Linux distro's package manager typically installs `mariadb-client` instead of the real `mysql-client`. This is due to the licensing terms of MariaDB (GPL) versus MySQL (Dual License). Oracle doesn't publish ARM64 packages either, so there's no easy way to install it if you're running on Apple Silicon.

MariaDB's client typically works fine against a MySQL server, but the differences can quickly become a problem. Some examples include the `mysqldump` output sometimes being incompatible, and plugin support such as `caching_sha2_password` diverging.

The fix for this is to copy the `mysql` and `mysqldump` binaries from the official Docker MySQL image, which supports ARM64, into your own image.

```dockerfile
FROM php:8.4-cli

COPY --from=mysql:8.4.4 /usr/bin/mysql /usr/local/bin/mysql
COPY --from=mysql:8.4.4 /usr/bin/mysqldump /usr/local/bin/mysqldump

RUN apt-get update && apt-get install -y libzip-dev && docker-php-ext-install zip
```

The `COPY --from=mysql:8.4.4` line pulls straight from the published image without needing a separate `FROM ... AS` build stage.
