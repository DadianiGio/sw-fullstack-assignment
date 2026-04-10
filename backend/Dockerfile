FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers

# Install PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy all backend files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Apache config — point to public folder
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 80