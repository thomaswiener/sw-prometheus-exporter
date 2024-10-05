#!/usr/bin/env bash
dir=`pwd`
./vendor/bin/phpunit --configuration="$dir" "$@"