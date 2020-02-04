#!/bin/bash
# Sometimes it's useful to know if Composer actually needs updating or if it was updated, for example for CI builds.

# 1) Check if you're running the latest version of Composer.
# - https://gist.github.com/lukechilds/a83e1d7127b78fef38c2914c4ececc3c
# - https://superuser.com/questions/363865/how-to-extract-a-version-number-using-sed
LATEST_COMPOSER_VERSION="$(curl --silent "https://api.github.com/repos/composer/composer/tags" | jq -r '.[0].name')" 
CURRENT_COMPOSER_VERSION="$(composer --version | sed -ne 's/[^0-9]*\(\([0-9]\.\)\{0,4\}[0-9][^.]\).*/\1/p')"

if [ $LATEST_COMPOSER_VERSION != $CURRENT_COMPOSER_VERSION ]; then 
    echo "Not using latest version"
    composer self-update
fi;

# 2) Update Composer version, and then do something if an update happened (otherwise nothing is done).
if [[ ! "$(composer self-update 2>&1)" == *"already using composer version"* ]]; then 
    echo "Upgraded to latest version"
else 
    echo "Already running latest version"
fi