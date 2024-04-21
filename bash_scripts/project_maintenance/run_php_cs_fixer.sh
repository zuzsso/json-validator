#!/bin/bash

# As seen in https://stackoverflow.com/questions/192292/how-best-to-include-other-scripts
DIR="${BASH_SOURCE%/*}"
if [[ ! -d "$DIR" ]]; then DIR="$PWD"; fi
. "$DIR/../common/constants.sh"

cd "$PROJECT_PATH" || exit

php $PHP_CS_FIXER_PATH fix --rules=@PSR12 --verbose $PROJECT_PATH/src
php $PHP_CS_FIXER_PATH fix --rules=@PSR12 --verbose $PROJECT_PATH/tests

