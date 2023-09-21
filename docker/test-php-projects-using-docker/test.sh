docker run --rm --interactive --tty --volume $PWD:/app composer install

docker run -it --rm --name laravel-dusk -v "$PWD":/usr/src/myapp -w /usr/src/myapp php:8.2-cli php vendor/bin/phpunit