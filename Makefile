build:
	npm i
run: build
	npm start
run-dev: build
	npm start -- --port=8001
esi-pdf: build
	docker run --rm -t --net=host -v `pwd`/slides:/slides astefanutti/decktape http://localhost:8000/presentations/01-esi/ 01-esi.pdf
	ps2pdf slides/01-esi.pdf slides/01-esi-small.pdf
esi-run: build
	docker stop nginx_ssi_example esi-nginx-1 esi-traefik-1 || true
	cd presentations/01-esi/demos/esi && make run-background
	cd presentations/01-esi/demos/ssi && make run-background
	sleep 5
	open "http://localhost:8000/presentations/01-esi/index.html"
	open "http://localhost:8888/"
	open "http://localhost:80/"
