#!/usr/bin/make
# Makefile readme (ru): <http://linux.yaroslavl.ru/docs/prog/gnu_make_3-79_russian_manual.html>
# Makefile readme (en): <https://www.gnu.org/software/make/manual/html_node/index.html#SEC_Contents>

SHELL = /bin/bash

container_fpm := php-fpm
container_cli := php-cli
container_cron := crontab

docker_compose_bin := sudo docker compose

docker_compose_exec := $(docker_compose_bin) exec --workdir="/app"
docker_compose_run := $(docker_compose_bin) run --workdir="/app" --service-ports --rm

docker_run_cli := $(docker_compose_run) "$(container_cli)" -c

.PHONY : help

.DEFAULT_GOAL := help

# This will output the help for each task. thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# ----------------------------------------------------------------------------------------------------------- [ Docker ]

up: ## Start all containers
	$(docker_compose_bin) up -d

down: ## Stop all started containers
	$(docker_compose_bin) down

restart: down up ## Stop and start again all started containers

up-build: ## Rebuild and start all containers
	$(docker_compose_bin) up -d --build --force-recreate --no-deps

rebuild: down up-build ## Stop all started containers, rebuild and start again

ps: ## List running containers
	$(docker_compose_bin) ps

status: ## List running containers (alias)
	$(docker_compose_bin) ps

logs: ## Show logs
	$(docker_compose_bin) logs

# ------------------------------------------------------------------------------------------------------------ [ Shell ]

shell-fpm: ## Run bash shell inside the FPM container
	$(docker_compose_exec) "$(container_fpm)" /bin/bash

shell-fpm-root: ## Run bash shell inside the FPM container with root privileges
	$(docker_compose_exec) -u root "$(container_fpm)" /bin/bash

shell-cron: ## Run bash shell inside the Crontab container with root privileges
	$(docker_compose_exec) "$(container_cron)" /bin/bash

shell: ## Run bash shell inside the CLI container
	$(docker_compose_run) "$(container_cli)"

shell-root: ## Run bash shell inside the CLI container with root privileges
	$(docker_compose_run) -u root "$(container_cli)"

shell-exec: ## Execute command in the CLI shell (via c=%command% variable)
	${docker_run_cli} "$(c)"

command: ## Run Symfony command in the CLI shell (via c=%command% variable)
	${docker_run_cli} "bin/console $(c)"

# ------------------------------------------------------------------------------------------------------------ [ Tests ]
test: ## Execute tests
	${docker_run_cli} "composer --colors=always --testdox test"

# ------------------------------------------------------------------------------------------------------------ [ Cache ]

cache-clear: ## Clear cache
	${docker_run_cli} "bin/console cache:clear"

# --------------------------------------------------------------------------------------------------------- [ Composer ]

composer-install: ## Composer - install vendors
	${docker_run_cli} "composer install"