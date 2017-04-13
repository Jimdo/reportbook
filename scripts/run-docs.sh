#!/bin/sh

docker run -d \
    --name ReadTheDocs \
    -p 8000:8000 \
    -v $(PWD)/docs/:/www/readthedocs.org/docs/ \
    shaker/readthedocs