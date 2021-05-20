FROM php:7.4-apache
ENV DEBIAN_FRONTEND noninteractive
ENV PHPREDIS_VERSION php7
RUN docker-php-source extract
RUN apt-get update && apt-get install -y git default-mysql-client zlib1g-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libonig-dev
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install bcmath
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && docker-php-ext-install gd
RUN git clone https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis
RUN docker-php-ext-install redis
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN a2enmod rewrite
