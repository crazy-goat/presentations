build:
	[ -d "go-esi" ] || git clone https://github.com/darkweak/go-esi.git
run: build
	docker-compose up
run-background: build
	docker-compose up -d