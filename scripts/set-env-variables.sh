#!/bin/sh

export DOCKER_IP=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.Gateway}}{{end}}' reportbook)
