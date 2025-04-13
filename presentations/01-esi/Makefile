PWD := $(shell pwd)

build:
	docker build --target base --progress=plain -t crazygoat/presentation-01-esi:latest .

push: build
	docker push crazygoat/presentation-01-esi:latest

build-dev:
	docker build --target dev --progress=plain -t crazygoat/presentation-01-esi:dev .

run:
	 docker run --rm --name presentation-01-esi -p 3210:80 -p 8080:8080 crazygoat/presentation-01-esi:latest

run-dev: build-dev
	 docker run --rm --name presentation-01-esi -p 3210:80 -v $(PWD)/slides:/var/www/slides -v $(PWD)/demo:/var/www/demo crazygoat/presentation-01-esi:dev