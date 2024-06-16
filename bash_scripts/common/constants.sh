#!/bin/bash

# shellcheck disable=SC2034

SCRIPT_PATH=$(dirname "$(readlink -f "$0")");
PROJECT_PATH="$SCRIPT_PATH/../../"

PHP_CS_FIXER_PATH="${PROJECT_PATH}vendor/bin/php-cs-fixer"
