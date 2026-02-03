FROM php:8.4-cli

# تثبيت الإضافات
RUN docker-php-ext-install pdo pdo_mysql

# نسخ المشروع
WORKDIR /app
COPY . .

# فتح البورت
EXPOSE 8080

# تشغيل السيرفر من public
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
