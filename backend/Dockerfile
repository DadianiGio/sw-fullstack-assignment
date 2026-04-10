FROM php:8.1-apache

# Enable Apache modules
RUN a2enmod rewrite headers

# Install PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Install Composer directly
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (for better Docker layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

# Copy rest of backend files
COPY . .

# Generate autoloader with full source
RUN composer dump-autoload --optimize

# Apache config — point to public folder
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 80