#!/usr/bin/env bash

docker build -t classified -f build/app/Dockerfile .
docker build -t classified-dev-sql -f build/sql/Dockerfile .