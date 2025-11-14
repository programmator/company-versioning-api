FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

RUN apk update && apk add --no-cache \  
    git \  
    curl \  
    oniguruma-dev \  
    libxml2-dev \  
    libzip-dev \  
    libpng-dev \  
    libwebp-dev \  
    libjpeg-turbo-dev \  
    freetype-dev \  
    postgresql-dev \  
    libmemcached-dev \  
    zlib-dev \  
    zip \  
    unzip \  
    autoconf \  
    g++ \  
    make \  
    linux-headers \  
    $PHPIZE_DEPS

RUN pecl install pcov \
    && docker-php-ext-enable pcov

RUN rm -rf /var/cache/apk/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \  
    && docker-php-ext-install gd \  
    && docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl bcmath opcache 

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

COPY . /var/www/html

RUN git config --global --add safe.directory /var/www/html

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
