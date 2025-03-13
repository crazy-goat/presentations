build:
	docker build . --tag crazygoat/nginx-ssi-example
run: build
	docker run --rm --name nginx_ssi_example -p 8888:80 crazygoat/nginx-ssi-example:latest