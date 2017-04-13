#!/bin/sh

docker run -d \
    --name ReadTheDocs \
    -p 8000:8000 \
    shaker/readthedocs