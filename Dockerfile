FROM php:8.2-apache

RUN usermod -u 1000 www-data; \
    groupmod -g 1000 www-data
