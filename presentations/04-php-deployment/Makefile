PWD  := $(shell pwd)
NAME := 04-php-deployment
PHP_MICRO_VERSION := 8.3.20
UNAME_M := $(shell uname -m)
ARCH := $(if $(filter aarch64 arm64,$(UNAME_M)),aarch64,x86_64)

single-file:
	mkdir -p webman/build
	[ -f webman/build/php8.3.micro.sfx ] || ( \
		curl -fsSL "https://dl.static-php.dev/static-php-cli/minimal/php-$(PHP_MICRO_VERSION)-micro-linux-$(ARCH).tar.gz" -o /tmp/micro.tar.gz && \
		tar -xzf /tmp/micro.tar.gz -C webman/build && \
		mv webman/build/micro.sfx webman/build/php8.3.micro.sfx && \
		rm /tmp/micro.tar.gz )
	cd webman && composer install --no-dev --optimize-autoloader --no-interaction --quiet
	cd webman && php -d phar.readonly=0 build-phar.php
	@echo "Done: webman/build/webman.bin"

build:
	docker build --progress=plain --platform linux/amd64 -t crazygoat/presentation-$(NAME):latest .

push: build
	docker push crazygoat/presentation-$(NAME):latest

run:
	docker run --rm --name presentation-$(NAME) -p 3210:8080 crazygoat/presentation-$(NAME):latest

run-dev:
	cd webman && PORT=3210 php start.php start

stop-dev:
	cd webman && php start.php stop

clean:
	docker stop presentation-$(NAME) || true
	docker image rm crazygoat/presentation-$(NAME):latest || true
