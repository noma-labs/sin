FROM php:7.4-fpm

# Copy composer.lock and composer.json
COPY composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev   # required while installing mbstring

# install node version 10
RUN curl -sL https://deb.nodesource.com/setup_10.x -o setup_node.sh
RUN chmod +x setup_node.sh
RUN bash setup_node.sh
RUN apt-get install -y nodejs
RUN apt-get install -y npm

# Clear cache
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#RUN rm -f setup_node.sh

# Install extensions
RUN docker-php-ext-install gd zip pdo_mysql mbstring zip exif pcntl
#RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/

# Install composer (VERSION 1.8.5 for an old laravel poject)
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --version=1.8.5 --filename=composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --version=2.1.12 --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www


# Copy existing application directory contents
COPY . /var/www

# Install node dependencies
#RUN npm i

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]