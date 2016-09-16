.PHONY: help tests update bootstrap lint doc server build push deploy login
.DEFAULT_GOAL := help

REGISTRY = https://index.docker.io/v1/
NAME     = reportbook
IMAGE    = jimdo/$(NAME)
WL       = ./wl

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
	docker build -t $(IMAGE) .

push: login ## Push container image to hub.docker.com
	docker push $(IMAGE)

deploy: build push $(WL) ## Deploy the app to the wonderland
	$(WL) deploy --watch $(NAME)

login:
	@docker login -u="$(DOCKER_LOGIN)" -p="$(DOCKER_PASSWORD)" $(REGISTRY)

$(WL):
	curl -sSo $(WL) https://downloads.jimdo-platform.net/wl/latest/wl_latest_$(shell uname -s | tr A-Z a-z)_$(shell uname -m | sed "s/x86_64/amd64/")
	chmod +x $(WL)
	$(WL) version
