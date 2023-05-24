#!/bin/sh

docker build --tag relate .


docker rmi -f $(docker images -q --filter label=stage=intermediate)
