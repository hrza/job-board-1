#!/usr/bin/env bash

section() {
    printf "\n\n\n================\n"
    printf "$1"
    printf "\n================\n"
}

if ! [ -f "./composer.json" ]
then
	echo "Please navigate to project root dir and start script"
fi

section "Installing..."

/usr/local/bin/composer install

section "Creating db"
php bin/console doctrine:database:create

section "Migrating"

php bin/console doc:mig:mig --no-interaction

section "Clearing production cache"

php bin/console cache:clear --env=prod

section "Done"






