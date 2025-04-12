PWD := $(shell pwd)

go-esi:
	git clone https://github.com/darkweak/go-esi.git

vendor: go-esi
	rm -rf go-esi/.git

build: vendor
	docker build --target base -t crazygoat/presentation-01-esi:latest .

build-dev: vendor
	docker build --target dev -t crazygoat/presentation-01-esi:dev .

run:
	 docker run --rm --name presentation-01-esi -p 3210:80 -p 8080:8080 crazygoat/presentation-01-esi:latest

run-dev: build-dev
	 docker run --rm --name presentation-01-esi -p 3210:80 -v $(PWD)/slides:/var/www/slides -v $(PWD)/demo:/var/www/demo crazygoat/presentation-01-esi:dev