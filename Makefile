.PHONY: help
help: ## Display this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

all: cs phpstan phpunit ## Executes all tasks

cs: ## Check Coding Style (php-cs-fixer)
	vendor/bin/php-cs-fixer fix

phpstan: ## Start static analysis
	vendor/bin/phpstan analyze src

phpunit: ## Execute tests
	vendor/bin/phpunit
