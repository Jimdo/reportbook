.PHONY: help tests update bootstrap lint doc server build push deploy login docker-setup mongo-server mongo-client
.DEFAULT_GOAL := help

REGISTRY = https://index.docker.io/v1/
NAME     = reportbook
IMAGE    = jimdo/$(NAME)
WL       = ./wl

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-12s\033[0m %s\n", $$1, $$2}'

tests: ## Execute test suite and create code coverage report
	./scripts/run-tests.sh

unit-tests: ## Execute test suite and create code coverage report
	./scripts/run-testsuite.sh unit

functional-tests: ## Execute test suite and create code coverage report
	./scripts/run-testsuite.sh functional

story-tests: ## Execute test suite and create code coverage report
	./scripts/run-testsuite.sh stories

update: ## Update composer packages
	./scripts/composer update

bootstrap: ## Install composer
	./scripts/install-composer.sh && ./scripts/composer install

lint: ## Lint all the code
	./scripts/phpcs --standard=PSR2 --encoding=utf-8 -p src

server: ## Start up local development web server
	./scripts/run-server.sh

setup: ## Setup server
	docker-compose exec mysql ./scripts/setup-mysql-server.sh
	docker-compose exec mongo ./scripts/setup-mongo-server.sh

storage-reset: ## Reset all volumes and services from docker-compose
	docker-compose down -v

bench: ## Starts the benchmarks
	./scripts/run-bench.sh

build: ## Generate docker container image
	docker build -t $(IMAGE) .

build-debug: build ## Generate docker container image
	docker build -t $(IMAGE):debug -f Dockerfile.debug .

build-cron: ## Generate docker container for mongoDB backup job
	docker build -t registry.jimdo-platform.net/$(NAME)-mongo-backup cron

build-test: ## Generate docker container for running tests
	docker build -t $(IMAGE)-test -f Dockerfile.test .

push: login ## Push container image to hub.docker.com
	docker push $(IMAGE)

push-test: ## Push reportbook test container image to hub.docker.com
	docker push $(IMAGE)-test

run-circleci-test:
	docker-compose -f docker-compose-circleci.yml run test

push-cron: ## Push mongoDB cron job container to wonderland registry
	docker push registry.jimdo-platform.net/$(NAME)-mongo-backup

deploy: build push $(WL) ## Deploy the app to the wonderland
	$(WL) deploy --watch $(NAME)

deploy-cron: $(WL) ## Deploy the mongoDB backup cron job to the wonderland
	$(WL) cron create reportbook-mongo-backup -f cron/cron.yaml
	wl cron status reportbook-mongo-backup

login:
	@docker login -u=$(DOCKER_USERNAME) -p=$(DOCKER_PASSWORD) $(REGISTRY)

$(WL):
	curl -sSLfo $(WL) https://downloads.jimdo-platform.net/wl/latest/wl_latest_$(shell uname -s | tr A-Z a-z)_$(shell uname -m | sed "s/x86_64/amd64/")
	chmod +x $(WL)
	$(WL) version

mysql-client: ## Connects to mysql
	./scripts/mysql-client.sh

mongo-client: ## Connects to mongoDB
	./scripts/mongo-client.sh
