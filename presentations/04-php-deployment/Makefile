PWD  := $(shell pwd)
NAME := 04-php-deployment

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
