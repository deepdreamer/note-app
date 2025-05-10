database-set-up:
	docker-compose exec php php bin/console doctrine:migrations:migrate && php bin/console doctrine:fixtures:load

install-project:
	docker-compose build && docker-compose up -d

start-project:
	docker-compose up -d

restart-project:
	docker-compose down && docker-compose up -d --force-recreate --remove-orphans

rebuild-project:
	docker-compose down && docker-compose build && docker-compose up -d --force-recreate --remove-orphans

run-tests-of-endpoints:
	docker-compose exec php php bin/phpunit

lint:
	docker-compose exec php composer phpcs

lint-fix:
	docker-compose exec php composer phpcbf

lint-phpstan:
	docker-compose exec php composer phpstan


