# k8s/base/Dockerfile

ARG WORDPRESS_TAG=php8.3-apache
FROM wordpress:${WORDPRESS_TAG}

# seed: wp-content
COPY wp-content/ /seed/wp-content/

# seed: wp-config.php / php.ini（追加）
COPY wp-config.php /seed/wp-config.php
COPY php.ini /seed/uploads.ini
