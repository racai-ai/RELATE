#!/bin/sh

mkdir -p /data/tmp/RELATE-run/{corpora,runnerq,logs}
mkdir -p /data/tmp/RELATE-run/logs/apache2

docker run --name "relate-run" -d -p=8001:80 \
    -v /data/tmp/RELATE-run/corpora:/site/DB/corpora \
    -v /data/tmp/RELATE-run/runnerq:/site/scripts/runnerq \
    -v /data/tmp/RELATE-run/logs:/var/log \
    relate

docker ps

