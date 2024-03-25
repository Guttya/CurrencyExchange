init: docker-down-clear docker-pull docker-build docker-up init-app
before-deploy: php-lint php-cs php-stan psalm test

up: docker-up
down: docker-down
restart: down up

rebuild: docker-down-clear docker-pull docker-build docker-up
recreate-database: database-drop database-create
init-app: composer-install recreate-database migrations-up style fixtures create-default-admin

cache-clear:
	docker compose run --rm fpm php bin/console cache:clear
	docker compose run --rm fpm php bin/console cache:warmup

stub-composer-operation:
	docker compose run --rm fpm composer require ...

debug-router:
	docker compose run --rm fpm php bin/console debug:router

docker compose-override-init:
	cp docker-compose.override-example.yml docker-compose.override.yml

docker-up:
	docker compose up -d

docker-rebuild:
	docker compose down -v --remove-orphans
	docker compose up -d --build

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build

style:
	docker compose run node sh -c "yarn"
	docker compose run node sh -c "yarn encore dev"

create-default-admin:
	docker compose run --rm fpm bin/console app:auth:user:create-admin admin@dev.com root

fixtures:
	docker compose run --rm fpm php bin/console doctrine:fixtures:load --no-interaction
	docker compose run --rm fpm php bin/console doctrine:fixtures:load --no-interaction --env=test

make-migration:
	docker compose run --rm fpm bin/console doctrine:migrations:diff

migrations-next:
	docker compose run --rm fpm php bin/console doctrine:migrations:migrate next -n
	docker compose run --rm fpm php bin/console --env=test doctrine:migrations:migrate next -n

migrations-prev:
	docker compose run --rm fpm php bin/console doctrine:migrations:migrate prev -n
	docker compose run --rm fpm php bin/console --env=test doctrine:migrations:migrate prev -n

migrations-up:
	docker compose run --rm fpm php bin/console doctrine:migrations:migrate --no-interaction
	docker compose run --rm fpm php bin/console doctrine:migrations:migrate --no-interaction --env=test

migrations-down:
	docker compose run --rm fpm php bin/console doctrine:migrations:migrate prev --no-interaction
	docker compose run --rm fpm php bin/console doctrine:migrations:migrate prev --no-interaction --env=test

database-create:
	docker compose run --rm fpm php bin/console doctrine:database:create --no-interaction --if-not-exists
	docker compose run --rm fpm php bin/console doctrine:database:create --no-interaction --env=test --if-not-exists

database-drop:
	docker compose run --rm fpm php bin/console doctrine:database:drop --force --no-interaction --if-exists
	docker compose run --rm fpm php bin/console doctrine:database:drop --force --no-interaction --env=test --if-exists

test:
	docker compose run --rm php ./vendor/bin/phpunit

psalm:
	docker compose run --rm php ./vendor/bin/psalm --no-cache $(ARGS)

php-lint:
	docker compose run --rm php ./vendor/bin/phplint

php-stan:
	docker compose run --rm php ./vendor/bin/phpstan --memory-limit=-1

php-cs:
	docker compose run --rm php ./vendor/bin/php-cs-fixer fix -v --using-cache=no
	docker compose run --rm php ./vendor/bin/php-cs-fixer fix --dry-run --diff --using-cache=no

composer-install:
	docker compose run --rm fpm composer install

composer-dump:
	docker compose run --rm fpm composer dump-autoload

composer-update:
	docker compose run --rm fpm composer update

composer-outdated:
	docker compose run --rm fpm composer outdated

composer-dry-run:
	docker compose run --rm fpm composer update --dry-run

load_dump:
	docker compose exec passport-postgres-db psql -U default -d "passport-db" -c "CREATE TABLE invalid_passport_temp (series_number varchar(255));"
	docker compose exec passport-postgres-db psql -U default -d "passport-db" -c "\copy invalid_passport_temp(series_number) FROM 'dump/dump.csv' CSV HEADER DELIMITER '&'"

secrets:
	werf helm secret values edit .helm/secret-values.yaml

consume-all:
	@docker compose exec php bin/console messenger:consume \
	common-command-transport \
	outgoing-webhook \
