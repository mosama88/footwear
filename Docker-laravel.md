<!-- -- Create Dockerfile -->

FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]


<!-- ----------------------------------------------- -->

<!-- Create docker-compose.yml -->

services:

  app:
    build:
      context: .
      dockerfile: Dockerfile

    container_name: laravel12_app

    working_dir: /var/www/html

    volumes:
      - ./:/var/www/html

    ports:
      - "8000:8000"

    depends_on:
      - mysql

    networks:
      - laravel

  nginx:
    image: nginx:latest

    container_name: laravel12_nginx

    ports:
      - "8080:80"

    volumes:
      - ./:/var/www/html
      - ./default.conf:/etc/nginx/conf.d/default.conf

    depends_on:
      - app

    networks:
      - laravel

  mysql:
    image: mysql:8.4

    container_name: laravel12_mysql

    restart: unless-stopped

    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: footwear_db
      MYSQL_USER: laravel
      MYSQL_PASSWORD: 123456

    ports:
      - "3307:3306"

    volumes:
      - mysql_data:/var/lib/mysql

    networks:
      - laravel

volumes:
  mysql_data:

networks:
  laravel:

  <!-- ----------------------------------------------- -->
<!-- إنشاء Laravel 12

بعد تشغيل الـ Containers: -->

docker compose up -d --build

  <!-- ----------------------------------------------- -->
<!-- ادخل على الكونتينر: -->

docker exec -it laravel12_app bash

  <!-- ----------------------------------------------- -->
<!-- ثبت Laravel 12: -->

composer create-project laravel/laravel:^12.0 .

  <!-- ----------------------------------------------- -->
<!-- شغل السيرفر: -->
php artisan serve --host=0.0.0.0 --port=8000

  <!-- ----------------------------------------------- -->

<!-- إعدادات .env -->

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

<!-- ثم: -->

php artisan migrate