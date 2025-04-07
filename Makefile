build:
	npm i
run: build
	npm start
run-dev: build
	npm start -- --port=8001
esi-pdf: build
	docker run --rm -t --net=host -v `pwd`/slides:/slides astefanutti/decktape http://localhost:8000/presentations/01-esi/ 01-esi.pdf
	ps2pdf slides/01-esi.pdf slides/01-esi-small.pdf