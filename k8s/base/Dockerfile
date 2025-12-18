# k8s/base/Dockerfile

ARG WORDPRESS_TAG=php8.3-apache
FROM wordpress:${WORDPRESS_TAG}

# k8s/base/wp-content → /seed/wp-content に同梱
COPY wp-content/ /seed/wp-content/
