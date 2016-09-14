.PHONY: help tests update bootstrap lint doc server build
.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-12s\033[0m %s\n", $$1, $$2}'

tests: ## Execute test suite and create code coverage report
	./scripts/phpunit

update: ## Update composer packages
	./scripts/composer update

bootstrap: ## Install composer
	./scripts/install-composer.sh && ./scripts/composer install

lint: ## Lint all the code
	./scripts/phpcs --standard=PSR2 --encoding=utf-8 -p src

doc: ## Generate documentation
	./scripts/phpdoc

server: ## Start up local development web server
	php -S localhost:8000 -t app/ app/router.php

build: ## Generate docker container image
	docker build -t jimdo/reportbook .
