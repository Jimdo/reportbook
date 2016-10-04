FROM ubuntu:16.04

MAINTAINER hauke.stange@jimdo.com

RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv EA312927 && \
    echo "deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.2 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-3.2.list

RUN apt-get update && apt-get install -y \
    awscli \
    mongodb-org-shell \
    mongodb-org-tools

WORKDIR /backup

COPY mongo-dump.sh /backup

ENTRYPOINT ["./mongo-dump.sh"]
