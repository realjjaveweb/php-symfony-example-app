.DEFAULT_GOAL := help

help: ## show this help (bash only)
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## starts services (detached)
	docker-compose up -d

down: ## stops and removes service containers
	docker-compose down

restart: down up ## stop, remove and recreate service containers (down up)

install: ## runs the initial install
	docker-compose exec api composer install
	docker-compose exec api bin/console --no-interaction doctrine:migrations:migrate

clear_cache: ## clears (symfony) cache
	docker-compose exec api bin/console cache:clear

tools_install:
	docker-compose exec api composer install --working-dir=tools/php-cs-fixer

format_dry_run: ## checks php formatting in a "dry" run, only showing what files would be affected
	docker-compose exec api tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --config ./.php-cs

format_fix: ## actually runs php formatting and does FILE CHANGES
	docker-compose exec api tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config ./.php-cs
