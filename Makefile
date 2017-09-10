NAME = ostretsov_foobargen

.PHONY: build
build:
	docker build -t $(NAME) .

.PHONY: sh
sh:
	docker run -ti -v $(PWD):/src $(NAME) /bin/sh

.PHONY: init
init:
	cp -n Dockerfile.dist Dockerfile

.PHONY: nginx
nginx:
	docker run -t -p 80:80 -v `pwd`/web:/usr/share/nginx/html:ro nginx:latest nginx -g "daemon off;"