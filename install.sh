#!/bin/bash

clear
echo "Choose the action:"
echo "1 - install"
echo "2 - update database"
echo "3 - quit"

read Keypress

case "$Keypress" in
1) echo "installing project is starting..."
    composer install
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force
    php bin/console h:d:f:l -n

;;
2) echo "updating database is starting..."
    php bin/console doctrine:database:drop --force
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force
    php bin/console h:d:f:l -n

;;
3) exit 0
;;
esac

exit 0