FROM php:5.6-apache

RUN echo "deb http://archive.debian.org/debian/ stretch main" > /etc/apt/sources.list \
 && echo "deb http://archive.debian.org/debian-security stretch/updates main" >> /etc/apt/sources.list \
 && apt-get update \
 && apt-get -y install git \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*
