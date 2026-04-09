PWD  := $(shell pwd)
NAME := 04-php-deployment

build:
	docker build --progress=plain --platform linux/amd64 -t crazygoat/presentation-$(NAME):latest .

push: build
	docker push crazygoat/presentation-$(NAME):latest

run:
	docker run --rm --name presentation-$(NAME) -p 3210:8080 crazygoat/presentation-$(NAME):latest

run-dev:
	docker run --rm --name presentation-$(NAME) -p 3210:8080 \
		-v $(PWD)/slides:/slides \
		crazygoat/presentation-$(NAME):latest

clean:
	docker stop presentation-$(NAME) || true
	docker image rm crazygoat/presentation-$(NAME):latest || true
