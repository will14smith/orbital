Build
=====

```
cd orbital/

pushd assets/
npm install
gulp

docker build -t orbital_php -f Dockerfile-php .
docker build -t orbital_web -f Dockerfile-nginx .

docker login
docker images
# find orbital_php & orbital_web image ids
docker tag <image id> will14smith/orbital_php:latest
docker tag <image id> will14smith/orbital_web:latest

docker push will14smith/orbital_php:latest
docker push will14smith/orbital_web:latest
```

Run
===

```

# install docker
mkdir /opt/
mkdir /opt/bin
curl -L "https://github.com/docker/compose/releases/download/1.8.1/docker-compose-$(uname -s)-$(uname -m)" > /opt/bin/docker-compose
chmod +x /opt/bin/docker-compose

curl https://raw.githubusercontent.com/ICAC/orbital/master/docker-compose.yml -o docker-compose.yml
curl https://raw.githubusercontent.com/ICAC/orbital/master/infrastructure/env.template -o .env 
nano .env

docker-compose build
docker-compose up

# db creation & migration

docker exec -it orbital_php_1 script /dev/null -c "cd /orbital && php app/console doctrine:database:create"
docker exec -it orbital_php_1 script /dev/null -c "cd /orbital && php app/console doctrine:migrations:migrate"

# file uploads?

```