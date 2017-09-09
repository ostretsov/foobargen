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