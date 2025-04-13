PWD := $(shell pwd)
NAME := 00-template


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
	docker run --rm --name presentation-$(NAME) -p 3210:8081 -p 35729:35729 -v $(PWD)/slides:/var/www/slides crazygoat/presentation-$(NAME):dev