SHELL := /usr/bin/env bash

PHP ?= php
COMPOSER ?= composer
NPM ?= npm

default: help

help: ## Show this help
	@grep -E '^[a-zA-Z0-9_.-]+:.*?##' $(MAKEFILE_LIST) | sed -E 's/:.*##/: /' | sort

install: ## Install PHP & Node dependencies
	$(COMPOSER) install --no-interaction
	$(NPM) install

dev: ## Run concurrent dev environment
	$(COMPOSER) run dev

qa: ## Run lint + static analysis
	$(COMPOSER) run check

qa-full: ## Run full quality gate (pint + phpstan + tests)
	$(COMPOSER) run check-all

quality: ## Alias for full quality gate
	$(COMPOSER) run quality

test: ## Run test suite
	$(PHP) artisan test

coverage: ## Run tests with coverage (Xdebug required)
	$(PHP) -d xdebug.mode=coverage artisan test --coverage-text

build: ## Production asset build
	$(NPM) run build

bootstrap: ## Run bootstrap script
	powershell -NoProfile -ExecutionPolicy Bypass -File ./tools/bootstrap.ps1

docs: ## Regenerate DOCX docs (modules + env setup)
	python scripts/env_setup_md_to_docx.py || true
	python scripts/md_to_docx.py || true

clean: ## Remove vendor & node_modules
	rm -rf vendor node_modules

fresh: ## Fresh migrate and seed
	$(PHP) artisan migrate:fresh --seed

sail-up: ## Start Sail
	bash ./vendor/bin/sail up -d

sail-build: ## Build Sail containers
	bash ./vendor/bin/sail build --no-cache

sail-test: ## Run tests inside Sail
	bash ./vendor/bin/sail php artisan test

.PHONY: help install dev qa qa-full test coverage build docs clean fresh sail-up sail-build sail-test
