HOST_SOURCE_PATH=$(shell pwd)
CONTAINER_SOURCE_PATH=/var/www/app
USER_ID=$(shell id -u)
GROUP_ID=$(shell id -g)
IMAGE_NAME=rmq-scope

#------------------------------------------------------------------------------

exec = docker run --rm --name rmq-scope \
                     -v ${HOST_SOURCE_PATH}:${CONTAINER_SOURCE_PATH} \
                     -w ${CONTAINER_SOURCE_PATH} \
                     -u ${USER_ID}:${GROUP_ID} \
                     --network="naoned" \
                     ${IMAGE_NAME} \
                     php $1

whalephant = docker run --rm --name whalephant \
                             -v ${HOST_SOURCE_PATH}:/var/www/app \
                             -w /var/www/app \
                             -u ${USER_ID}:${GROUP_ID} \
                             php:7.2-cli \
                             ./whalephant generate $1

#------------------------------------------------------------------------------

publish-ir: create-image
	$(call exec, publishIR.php)

unpublish-ir: create-image
	$(call exec, unpublishIR.php)

publish-cdc: create-image
	$(call exec, publishCDC.php)

unpublish-cdc: create-image
	$(call exec, unpublishCDC.php)

create-image: docker/images/script/Dockerfile
	docker build -q -t ${IMAGE_NAME} docker/images/script/

docker/images/script/Dockerfile: whalephant
	$(call whalephant, docker/images/script)

whalephant:
	$(eval LATEST_VERSION := $(shell curl -L -s -H 'Accept: application/json' https://github.com/niktux/whalephant/releases/latest | sed -e 's/.*"tag_name":"\([^"]*\)".*/\1/'))
	@echo "Latest version of Whalephant is ${LATEST_VERSION}"
	wget -O whalephant -q https://github.com/Niktux/whalephant/releases/download/${LATEST_VERSION}/whalephant.phar
	chmod 0755 whalephant
