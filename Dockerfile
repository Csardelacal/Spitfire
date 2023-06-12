FROM php:8-apache

# PHP_CPPFLAGS are used by the docker-php-ext-* scripts, which in turn are required
# in order to build the intl extension.
ENV PHP_CPPFLAGS="$PHP_CPPFLAGS -std=c++11"

RUN apt-get update -y \
    && apt-get upgrade -y  \
    && apt-get install memcached libicu-dev git zip unzip zlib1g-dev -y 
	
RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-install opcache \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl 
 
# install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo 'xdebug.mode = debug' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.client_port=9000' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.start_with_request = yes' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/php.ini


RUN a2enmod rewrite
RUN service apache2 restart
    
WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.* ./
#RUN composer install --no-interaction --no-autoloader --no-dev
#RUN composer dump-autoload --no-interaction --optimize

# Enable the session directory being written to.
# In future revisions I would like to revert back to using the default directory, since
# it makes no sense to have this here.
RUN mkdir -p bin/usr/sessions
RUN mkdir -p bin/usr/uploads
RUN chown -R www-data: bin/usr

#TODO: Make the storage and public storage directories writable.
