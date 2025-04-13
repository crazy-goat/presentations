PWD := $(shell pwd)
NAME := 02-php-runtime


build:
	docker build --target base --progress=plain -t crazygoat/presentation-$(NAME):latest .

push: build
	docker push crazygoat/presentation-$(NAME):latest

build-dev:
	docker build --target dev --progress=plain -t crazygoat/presentation-$(NAME):dev .

run:
	 docker run --rm --name presentation-$(NAME) -p 3210:80 crazygoat/presentation-$(NAME):latest

run-dev: build-dev
	cd slides && npm i && cd ..
	docker run --rm --name presentation-$(NAME)-dev -p 3210:8081 -p 35729:35729 -v $(PWD)/slides:/var/www/slides crazygoat/presentation-$(NAME):dev

clean:
	rm -rf slides/node_modules
	docker stop presentation-$(NAME) presentation-$(NAME)-dev || true
	docker image rm crazygoat/presentation-$(NAME):latest crazygoat/presentation-$(NAME):dev || true