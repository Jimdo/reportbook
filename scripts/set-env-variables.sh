#!/bin/sh

if [ -z ${SELENIUM_IP+x} ]; then
  export SELENIUM_IP=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.Gateway}}{{end}}' reportbook)
fi

export REPORTBOOK_IP=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.Gateway}}{{end}}' reportbook)
