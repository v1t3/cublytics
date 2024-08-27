init: pull build up migrate

pull:
	docker compose pull

build:
	docker compose build

up:
	docker compose up -d

migrate:
	docker compose run --rm php-fpm php bin/console doctrine:migrations:migrate --no-interaction