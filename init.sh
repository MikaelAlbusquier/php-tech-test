#!/bin/bash

mysql -uroot --execute="create database snaphr_test"

mysql -uroot snaphr_test < create_database.sql

php bin/console app:create-box --env=prod

php -S 127.0.0.1:8000 -t public

