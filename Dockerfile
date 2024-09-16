FROM php:8.1-apache
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install --no-install-recommends -y \
    libzip-dev \
    libxml2-dev \
    mariadb-client \
    zip \
    unzip \
    cron \
    curl \
    vim \
    wget \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
  && apt-get clean && rm -rf /var/lib/apt/lists/*
# Install PHP GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd
RUN pecl install redis zip pcov \
  && docker-php-ext-enable redis zip \
  && docker-php-ext-install pdo_mysql bcmath soap \
  && docker-php-source delete
RUN a2enmod rewrite \
  && a2enmod headers \
  && a2enmod expires
COPY dir.conf /etc/apache2/mods-enabled/dir.conf
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN chmod 755 /var/www/html && chown -R www-data:www-data /var/www/html
WORKDIR /var/www/html
COPY . /var/www/html
RUN chown -R www-data:www-data .
EXPOSE 80
CMD [ "sh", "-c", "apache2-foreground" ]