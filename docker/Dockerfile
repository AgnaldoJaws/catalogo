FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    vim \
    libpq-dev \
    libzip-dev \
    libbrotli-dev \
    brotli \
    libgmp-dev \
 && docker-php-ext-configure gmp \
 && docker-php-ext-install -j$(nproc) gmp pdo_pgsql zip pcntl \
 && echo "extension=gmp" > /usr/local/etc/php/conf.d/docker-php-ext-gmp.ini \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN pecl install swoole \
 && docker-php-ext-enable swoole

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get update && apt-get install -y nodejs \
 && rm -rf /var/lib/apt/lists/*

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /var/www/html

COPY . .

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8005

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
