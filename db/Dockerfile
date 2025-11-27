FROM mariadb:latest

MAINTAINER me

ENV MYSQL_DATABASE=chats \
    MYSQL_ROOT_PASSWORD=super-secret \
    MYSQL_USER=papu-secret \
    MYSQL_PASSWORD=super-secret

ADD ./dump.sql /docker-entrypoint-initdb.d

EXPOSE 3306

