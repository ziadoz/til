# Put this inside /bin/test.sh to install Laravel's dependencies before running testsuite in Docker.
docker run \
  -it \
  --rm \
  -w /data \
  -v ${PWD}:/data:delegated \
  --entrypoint /bin/sh \
  registry.gitlab.com/grahamcampbell/php:8.1-base -c 'curl -o /tmp/composer-setup.php https://getcomposer.org/installer && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && composer install'