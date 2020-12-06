## Requirements

- [Docker](https://www.docker.com/products/docker-desktop)
- [Node.js](https://nodejs.org/en/)

## Install

create environment variables with required configuraitons
`html/.env`
Build the php container and the composer
```sh
docker build -f Dockerfile -t extinctionrebellion/website:dev .
docker build -f composer.Dockerfile -t extinctionrebellion/composer:dev .
```

Lastly, follow the next step to run the website and then [pull the production database](/docs/sync-production-data.md)

## Run

First, you must activate Docker. Then:

```sh
# start website
docker-compose up -d
```

The website should be active at `http://localhost:8000`

## Shutdown

```sh
docker-compose down
```

This way of shutting down the website is likely to cause less errors than directly quitting Docker. 

## Install new plugins

If you want to install a new plugin
```sh
docker-compose exec composer require wpackagist-plugin/{{PLUGIN_NAME}}
```

If you want to update a plugin
```sh
docker-compose exec composer update wpackagist-plugin/{{PLUGIN_NAME}}
```

