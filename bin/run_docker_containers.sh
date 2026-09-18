docker run -t -i -d -p 3306:3306 --name sql -e MYSQL_ROOT_PASSWORD="devpasswd" classified-dev-sql
docker run -t -i -d -h localhost -p 443:443 classified