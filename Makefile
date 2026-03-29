COMPOSE_FILE=docker-compose.local.yml
DOCKER_COMPOSE=docker compose -f $(COMPOSE_FILE)
APP_SERVICE=app

.PHONY: help up down build rebuild logs bash iex test format db-console migrate setup

help: ## Mostra os comandos disponíveis
	@echo "Comandos disponíveis:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

# -------------------------------------------------------------------
# Containers
# -------------------------------------------------------------------
up: ## Sobe todos os containers em background (Ambiente Local)
	$(DOCKER_COMPOSE) up -d

up-build: ## Refaz o build e sobe os containers recriando-os
	$(DOCKER_COMPOSE) up -d --build --force-recreate

down: ## Derruba todos os containers e a rede local
	$(DOCKER_COMPOSE) down -v

build: ## Faz o build das imagens (sem subir os containers)
	docker-compose build

logs: ## Mostra os logs da aplicação em tempo real
	$(DOCKER_COMPOSE) logs -f $(APP_SERVICE)

# -------------------------------------------------------------------
# Elixir / Phoenix
# -------------------------------------------------------------------
bash: ## Entra no terminal (bash) do container da API
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) bash

iex: ## Abre o terminal interativo do Elixir conectado ao servidor Phoenix rodando
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) iex -S mix

deps: ## Instala dependências do Elixir
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) mix deps.get

test: ## Roda os testes do Elixir
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) mix test

format: ## Roda o formatador de código
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) mix format

# -------------------------------------------------------------------
# Ecto (Database)
# -------------------------------------------------------------------
setup: deps ## Prepara o banco de dados pela primeira vez (cria e migra)
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) mix ecto.setup

migrate: ## Roda as migrations do banco de dados
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) mix ecto.migrate

db-console: ## Acessa o terminal do PostgreSQL diretamente
	$(DOCKER_COMPOSE) exec db psql -U postgres -d wallet_dev