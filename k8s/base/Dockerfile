# k8s/base/Dockerfile

ARG WORDPRESS_TAG=php8.3-apache
FROM wordpress:${WORDPRESS_TAG}

# seed: wp-content（themes/plugins/languages/uploads を丸ごと含む）
COPY wp-content/ /seed/wp-content/

# seed: wp-config.php / php.ini（追加）
COPY wp-config.php /seed/wp-config.php
COPY php.ini /seed/uploads.ini

# /seed/wp-content を PVC へ反映するスクリプト。
# Deployment の initContainer と restore/sync Job が共通で使う。
COPY scripts/sync-wp-content.sh /usr/local/bin/sync-wp-content.sh
RUN chmod +x /usr/local/bin/sync-wp-content.sh
