FROM debian:bookworm-slim

RUN set -eux; \
  apt-get update -qq; \
  apt-get install -y -qq --no-install-recommends \
    ca-certificates \
    git \
    openssh-client \
    mariadb-client \
    rsync \
    inotify-tools \
    bash \
    tzdata; \
  rm -rf /var/lib/apt/lists/*

WORKDIR /work
ENTRYPOINT ["/bin/bash","-lc"]
