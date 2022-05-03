
.PHONY: start
start:	
	docker compose up -d 
	symfony server:start -d

.PHONY:stop
stop:	 
	symfony server:stop
	docker compose down

.PHONY: build
build:
	npm run build

.PHONY:test
test:
	php bin/phpunit

.PHONY:phpcs
phpcs:
	composer phpcs

