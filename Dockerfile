# Build stage for frontend assets
FROM node:22-bullseye AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY . .
RUN npm run build

# Production stage for PHP application
FROM php:8.2-cli-bullseye
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    zlib1g-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev && \
    docker-php-ext-install pdo pdo_mysql zip && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .
COPY --from=frontend /app/public/build /app/public/build

RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs && chmod -R 775 storage bootstrap/cache

EXPOSE 8000
CMD ["bash", "start.sh"]
