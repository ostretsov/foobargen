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

.PHONY: video
video:
	ffmpeg -loop 1 -r 2 -i $(POSTER) -i $(MP3) -vf "scale=trunc(iw/2)*2:trunc(ih/2)*2" -c:v libx264 -preset slow -tune stillimage -crf 18 -c:a copy -shortest -pix_fmt yuv420p -threads 0 $(OUT)