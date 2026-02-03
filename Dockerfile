FROM php:8.4-cli

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app
COPY . .

CMD sh -c 'PORT=${PORT:-8080} && php -S 0.0.0.0:$PORT -t public'
