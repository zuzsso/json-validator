#!/bin/bash

# As seen in https://stackoverflow.com/questions/192292/how-best-to-include-other-scripts
DIR="${BASH_SOURCE%/*}"
if [[ ! -d "$DIR" ]]; then DIR="$PWD"; fi
. "$DIR/../common/constants.sh"

cd "$PROJECT_PATH" || exit

$PHP_UNIT_BIN_PATH -c "$PROJECT_PATH"phpunit.xml.dist --display-phpunit-deprecations