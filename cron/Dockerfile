FROM ubuntu:16.04

MAINTAINER hauke.stange@jimdo.com

RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv EA312927 && \
    echo "deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.2 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-3.2.list

RUN apt-get update && apt-get install -y \
    python \
    unzip \
    curl \
    mongodb-org-shell \
    mongodb-org-tools

RUN curl "https://s3.amazonaws.com/aws-cli/awscli-bundle.zip" -o "awscli-bundle.zip"
RUN unzip awscli-bundle.zip
RUN ./awscli-bundle/install -i /usr/local/aws -b /usr/local/bin/aws

WORKDIR /backup

COPY mongo-dump.sh /backup

ENTRYPOINT ["./mongo-dump.sh"]
