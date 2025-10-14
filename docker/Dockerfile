FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    oniguruma-dev

# Install build dependencies temporarily for PHP extensions
RUN apk add --no-cache --virtual .build-deps \
    build-base \
    autoconf \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    gd \
    bcmath

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install PCOV for code coverage (lighter than Xdebug)
RUN pecl install pcov && docker-php-ext-enable pcov

# Remove build dependencies
RUN apk del .build-deps

# Copy PCOV configuration
COPY docker/php/pcov.ini /usr/local/etc/php/conf.d/pcov.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Copy existing application directory contents
COPY backend/ /var/www

# Copy existing application directory permissions
COPY --chown=www:www backend/ /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]