<!-- laravel-project/
│
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   │
│   └── php/
│       └── Dockerfile
│
├── docker-compose.yml
│
└── src/
    └── Laravel 12 Project
     -->


     <!-- Create Dockerfile -->

     FROM php:8.4-fpm

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
    supervisor \
    cron \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        exif \
        pcntl \
        mbstring \
        zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["php-fpm"]



<!-- ---------------------------------------------------------- -->


<!-- create default.conf -->

server {

    listen 80;

    index index.php index.html;

    server_name localhost;

    root /var/www/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {

        fastcgi_pass app:9000;

        fastcgi_index index.php;

        include fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}

<!-- ---------------------------------------------------------- -->
<!-- create docker-compose.yml -->

services:

  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile

    container_name: laravel_app

    working_dir: /var/www

    volumes:
      - ./src:/var/www

    depends_on:
      - mysql
      - redis

    networks:
      - laravel

  nginx:
    image: nginx:latest

    container_name: laravel_nginx

    ports:
      - "80:80"

    volumes:
      - ./src:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf

    depends_on:
      - app

    networks:
      - laravel

  mysql:
    image: mysql:8.4

    container_name: laravel_mysql

    restart: unless-stopped

    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel
      MYSQL_PASSWORD: secret

    ports:
      - "3306:3306"

    volumes:
      - mysql_data:/var/lib/mysql

    networks:
      - laravel

  redis:
    image: redis:latest

    container_name: laravel_redis

    ports:
      - "6379:6379"

    networks:
      - laravel

volumes:
  mysql_data:

networks:
  laravel:


<!-- ---------------------------------------------------------- -->
<!-- إنشاء Laravel 12 -->

composer create-project laravel/laravel:^12.0 .

<!-- ---------------------------------------------------------- -->

<!-- تشغيل المشروع -->

docker compose up -d --build

<!-- ---------------------------------------------------------- -->

<!-- الدخول للكونتينر -->
docker exec -it laravel_app bash

<!-- ---------------------------------------------------------- -->

<!-- تثبيت Dependencies -->
composer install

<!-- ---------------------------------------------------------- -->


<!-- ملف .env -->

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

CACHE_STORE=redis

QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PORT=6379

<!-- ---------------------------------------------------------- -->
<!-- تشغيل Migration -->

php artisan migrate

<!-- ---------------------------------------------------------- -->
<!-- تشغيل Queue Worker -->

docker exec -it laravel_app php artisan queue:work


<!-- تشغيل Scheduler -->

docker exec -it laravel_app php artisan schedule:work