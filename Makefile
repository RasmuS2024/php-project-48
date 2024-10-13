update:
	composer update

install:
	composer install --ignore-platform-reqs

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin

test:
	composer exec --verbose phpunit tests
