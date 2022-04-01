# php-comments

## Getting Started

- install [phpunit](https://phpunit.de/getting-started/phpunit-9.html)
  - wget -O phpunit https://phar.phpunit.de/phpunit-9.phar
  - chmod +x phpunit 
  - ./phpunit --version

## Run tests
```
cd php
./phpunit --bootstrap ./tests/autoload.php tests
```
## Run application with Docker

- execute docker-compose.yml
```
docker-compose up -d
```

- open browser at localhost:8000
