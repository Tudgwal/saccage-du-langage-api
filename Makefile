
# Variables
DOCKER_COMPOSE = docker-compose
DOCKER_COMPOSE_FILE = docker-compose.yml

# Targets
.PHONY: up down init migrate

up:
	@echo "--> build containers"
	$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) up -d

down:
	@echo "--> down containers"
	$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) down

init: up
	@echo "--> init project"
	$(DOCKER_COMPOSE) run symfony composer install
	$(DOCKER_COMPOSE) run symfony php bin/console lexik:jwt:generate-keypair

migrate: up
	@echo "--> migrate database"
	$(DOCKER_COMPOSE) run symfony php bin/console --no-interaction doctrine:migrations:migrate

bash: up
	@echo "--> enter in container"
	$(DOCKER_COMPOSE) exec symfony /bin/bash