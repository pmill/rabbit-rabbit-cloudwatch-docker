#!/bin/sh

composer install -d ./app
tar --exclude=.git -zcf app.tar.gz ./app *
docker build --no-cache -t pmill/rabbit-rabbit-cloudwatch:$1 .