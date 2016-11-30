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

update: ## Update composer packages
	./scripts/composer update

bootstrap: ## Install composer
	./scripts/install-composer.sh && ./scripts/composer install

lint: ## Lint all the code
	./scripts/phpcs --standard=PSR2 --encoding=utf-8 -p src

doc: ## Generate documentation
	./scripts/phpdoc

server: ## Start up local development web server
	./scripts/run-server.sh

bench: ## Starts the benchmarks
	./scripts/run-bench.sh

build: ## Generate docker container image
	docker build -t $(IMAGE) .

build-cron: ## Generate docker container for mongoDB backup job
	docker build -t registry.jimdo-platform.net/$(NAME)-mongo-backup cron

push: login ## Push container image to hub.docker.com
	docker push $(IMAGE)

push-cron: ## Push mongoDB cron job container to wonderland registry
	docker push registry.jimdo-platform.net/$(NAME)-mongo-backup

deploy: build push $(WL) ## Deploy the app to the wonderland
	$(WL) deploy --watch $(NAME)

deploy-cron: $(WL) ## Deploy the mongoDB backup cron job to the wonderland
	$(WL) cron run -f cron/cron.yaml
	wl cron status reportbook-mongo-backup

login:
	@docker login -u="$(DOCKER_LOGIN)" -p="$(DOCKER_PASSWORD)" $(REGISTRY)

$(WL):
	curl -sSo $(WL) https://downloads.jimdo-platform.net/wl/latest/wl_latest_$(shell uname -s | tr A-Z a-z)_$(shell uname -m | sed "s/x86_64/amd64/")
	chmod +x $(WL)
	$(WL) version

docker-setup:
	-docker-machine start
	./scripts/pull-image.sh mongo

mongo-server: docker-setup ## Starts up mongoDB
	./scripts/mongo-server.sh

mongo-client: ## Connects to mongoDB
	./scripts/mongo-client.sh

mongo-setup: ## Initial user setup
	./scripts/mongo-setup.sh

mongo-reset: ## Remove reportbook-data
	./scripts/mongo-volume-reset.sh
