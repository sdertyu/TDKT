# app/Dockerfile

# Stage 1: Build Vue.js
FROM node:18 as nodebuild

WORKDIR /app
COPY . .

# Cài đặt dependencies Vue.js
WORKDIR /app/frontend
RUN npm install && npm run build


# Stage 2: Build Laravel App
FROM php:8.2-fpm

# Cài đặt các extension PHP cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd pdo pdo_mysql

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy mã nguồn Laravel
COPY . .

# Copy file build của Vue.js từ Stage 1
COPY --from=nodebuild /app/frontend/dist /var/www/html/public

# Cài đặt các package PHP
RUN composer install

# Phân quyền cho thư mục storage và bootstrap
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
