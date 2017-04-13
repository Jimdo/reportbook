#!/bin/sh

docker run -it \
    --name ReadTheDocs \
    -p 8000:8000 \
    -v $(PWD)/docs/:/www/readthedocs.org/docs/ \
    shaker/readthedocs \
    bash