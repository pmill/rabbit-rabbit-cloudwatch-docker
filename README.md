# pmill/rabbit-rabbit-cloudwatch-docker

## Introduction

This repository contains a dockerfile that when run (as single-run or a daemon) will send RabbitMQ queue counts to 
Amazon CloudWatch.

# Usage

Single run example `Dockerfile`, when built as an image and ran it will poll the `/messages` queue and send the count to 
a metric on CloudWatch called `queue_messages`:

```
FROM pmill/rabbit-rabbit-cloudwatch:0.1.0
MAINTAINER Your Name <your@email.com>

COPY config.json /app/config.json

ENTRYPOINT ["php", "/app/index.php", "/app/config.json"]
```

Create a config file named `config.json`:

```json
{
  "run": "single",
  "rabbitmq": {
    "host": "rabbitmq:15672",
    "username": "guest",
    "password": "guest"
  },
  "cloudwatch": {
    "region": "eu-west-1",
    "key": "",
    "secret": ""
  },
  "metrics": {
    "queue_messages": {
      "vhost": "/",
      "queue": "messages"
    }
  }
}
```

## Build

To build the image, put your `Dockerfile` and your `config.json` file in a folder and then run the following commands:

```
docker build --no-cache -t my-image:1 .
docker run my-image:1
```

# Copyright

pmill/rabbit-rabbit-cloudwatch-docker
Copyright (c) 2018 pmill (dev.pmill@gmail.com) 
All rights reserved.