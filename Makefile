.PHONY: build72 build74 build80 build81 build82 build83 git-hooks composer-install composer-update shell tests-php7 tests-php8 phpstan psalm ecs-check ecs-fix code-quality coverage-php7 coverage-php8 pre-commit
IMAGE_NAME=smithsfjs-php-cli
PHPUNIT_PATH=./vendor/bin/phpunit
DOCKER_RUN=docker run  --volume $$(pwd):/var/www/html $(IMAGE_NAME)

build72:
	@docker build . -f Dockerfile -t $(IMAGE_NAME):latest --build-arg BASE_IMAGE=php:7.2-cli-alpine3.12 --build-arg XDEBUG_VERSION=3.1.6
	@make git-hooks

build74:
	@docker build . -f Dockerfile -t $(IMAGE_NAME):latest --build-arg BASE_IMAGE=php:7.4-cli-alpine3.16 --build-arg XDEBUG_VERSION=3.1.6
	@make git-hooks

build80:
	@docker build . -f Dockerfile -t $(IMAGE_NAME):latest --build-arg BASE_IMAGE=php:8.0-cli-alpine3.16 --build-arg XDEBUG_VERSION=3.3.1
	@make git-hooks

build81:
	@docker build . -f Dockerfile -t $(IMAGE_NAME):latest --build-arg BASE_IMAGE=php:8.1-cli-alpine3.19 --build-arg XDEBUG_VERSION=3.3.1
	@make git-hooks

build82:
	@docker build . -f Dockerfile -t $(IMAGE_NAME):latest --build-arg BASE_IMAGE=php:8.2-cli-alpine3.19 --build-arg XDEBUG_VERSION=3.3.1
	@make git-hooks

build83:
	@docker build . -f Dockerfile -t $(IMAGE_NAME):latest --build-arg BASE_IMAGE=php:8.3-cli-alpine3.19 --build-arg XDEBUG_VERSION=3.3.1
	@make git-hooks

git-hooks:
	@hooks=$$( find config/git-hooks/* | grep -vE '~' ); for hook in $$hooks; do ln -sf ../../$$hook .git/hooks/; done;

composer-install:
	@rm composer.lock || true
	@$(DOCKER_RUN) /usr/local/bin/composer install --no-interaction

composer-update:
	@$(DOCKER_RUN) /usr/local/bin/composer update --no-interaction

shell: DOCKER_RUN=docker run -it --volume $$(pwd):/var/www/html $(IMAGE_NAME)
shell:
	@$(DOCKER_RUN) sh

tests-php7:
	@$(DOCKER_RUN) vendor/bin/phpunit -c phpunit-8.xml.dist

tests-php8:
	@$(DOCKER_RUN) vendor/bin/phpunit -c phpunit-11.xml.dist

phpstan:
	@$(DOCKER_RUN) ./vendor/bin/phpstan analyse -c phpstan.neon.dist

psalm:
	@$(DOCKER_RUN) ./vendor/bin/psalm -c psalm.xml

ecs-check:
	@$(DOCKER_RUN) ./vendor/bin/ecs check -c ecs.php

ecs-fix:
	@$(DOCKER_RUN) ./vendor/bin/ecs check -c ecs.php --fix

code-quality:
	@make phpstan
	@make psalm
	@make ecs-check

coverage-php7: DOCKER_RUN=docker run  -e XDEBUG_MODE=coverage --volume $$(pwd):/var/www/html $(IMAGE_NAME)
coverage-php7:
	@$(DOCKER_RUN) vendor/bin/phpunit -c phpunit-8.xml.dist --coverage-html ./coverage

coverage-php8: DOCKER_RUN=docker run  -e XDEBUG_MODE=coverage --volume $$(pwd):/var/www/html $(IMAGE_NAME)
coverage-php8:
	@$(DOCKER_RUN) XDEBUG_MODE=coverage vendor/bin/phpunit -c phpunit-11.xml.dist --coverage-html ./coverage

pre-commit:
	config/git-hooks/pre-commit
