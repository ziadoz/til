#!/usr/bin/env bash
brew tap homebrew/homebrew-php
brew install php71
cd ~/.atom/packages/php-integrator-base/core
/usr/local/bin/php ./composer.phar create-project php-integrator/core ./3.0.0 3.0.0 --prefer-dist --no-dev