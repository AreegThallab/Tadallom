FROM php:8.4-cli

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app
COPY . .

CMD ["/bin/sh", "-lc", "PORT=$(printenv PORT || echo 8080); echo Using_PORT=$PORT; exec php -S 0.0.0.0:$PORT -t public"]
