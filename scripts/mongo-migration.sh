#!/bin/sh

# Create unique indices
mongo $MONGO_PROD_HOST -u $MONGO_PROD_USERNAME -p $MONGO_PROD_PASSWORD < scripts/mongo/create-indices.js
