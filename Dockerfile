FROM php:8.2-apache

# Fix MPM conflict - disable semua MPM dulu, lalu enable prefork saja
RUN a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Install Python3 + pip
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    && rm -rf /var/lib/apt/lists/*

# Install PyMuPDF
RUN pip3 install pymupdf --break-system-packages

# Copy Apache config
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Copy files
COPY public/ /var/www/html/

# Permissions
RUN mkdir -p /var/www/html/uploads /var/www/html/output \
    && chown -R www-data:www-data /var/www/html \
    && chmod 777 /var/www/html/uploads /var/www/html/output

EXPOSE 80
