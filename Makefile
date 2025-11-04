install:
	composer install

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 app

test:
	composer exec --verbose phpunit

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit -- --coverage-clover build/logs/clover.xml --coverage-html build/coverage

PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public