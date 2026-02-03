FROM php:8.4-cli

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app
COPY . .

# لا نحدد رقم ثابت
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT -t public"]
