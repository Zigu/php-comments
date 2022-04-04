# php-comments

## Getting Started

This project is a sample application for a CRUD API managing simple comments.

It uses a MySQL-8 database and PHP 7.1 running on an Apache webserver. All production 
files are located in `php/src`, all test files in `php/tests`.

The web application provides an API callable via `/comments` and a simple web view
available at `/` when using your browser. 
This is done by using an `.htaccess` file located in the webapps directory and having a
rewrite module (`mod_rewrite`) enabled.

### API calls with curl and using docker-compose

- Get comments (GET /comments): `curl http://localhost:8000/comments`
- Get comment (GET /comments/\<id\>): `curl http://localhost:8000/<id>`
- Create comment (POST /comments): `curl -X POST -H 'Content-Type: application/json' -d '{"author":"curl","text":"shell text"}' http://localhost:8000/comments`
- Update comment (PUT /comments/\<id\>): `curl -X PUT -H 'Content-Type: application/json' -d '{"author":"curl","text":"another shell text"}' http://localhost:8000/comments/<id>`
- Delete comment (DELETE /comments/\<id\>): `curl -X DELETE http://localhost:8000/comments/<id>`

## Run application with docker-compose

- execute docker-compose.yml
```
docker-compose up -d
```

- for web view, open browser at [http://localhost:8000](http://localhost:8000)
- for calling the API directly (e.g. via curl), use [http://localhost:8000/comments](http://localhost:8000/comments)
- the database will be accessible via port 9906

## Run application on host machine

- setup MySQL database
- apply `comments-table.sql` in `migrations` directory to database
- adjust database connection variables in `persistence/DatabaseConnector.php` or export the following environment variables. For example:
```
export DB_HOST=<your db host>
export DB_PORT=<your db port>
export DB_USERNAME=<your user with permissions to access DB>
export DB_PASSWORD=<db user password>
``` 
- copy all files located in `php/src` to your webapps folder (e.g. /var/www/html/ on Apache)
- ensure that your webserver provides URL rewriting (e.g. enable `mod_rewrite` on Apache) and 
respects the `.htaccess` file provided in the webapps folder
- for web view, open browser http://\<your webapp\>
- for calling the API directly (e.g. via curl), use http://\<your webapp\>/comments

## Run tests

- go to `php` folder
- if no phpunit file is present, install [phpunit](https://phpunit.de/getting-started/phpunit-9.html)
``` 
wget -O phpunit https://phar.phpunit.de/phpunit-9.phar
```
- execute `chmod +x phpunit`
- execute `run-tests.sh`
```
./run-tests.sh
```

- the run-tests.sh will create a test coverage in `php/coverage` directly
- in case run-tests.sh is not executable please execute `chmod +x run-tests.sh`
