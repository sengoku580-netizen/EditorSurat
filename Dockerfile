FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_PID_FILE=/var/run/apache2/apache2.pid
ENV APACHE_RUN_DIR=/var/run/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    libapache2-mod-php8.1 \
    python3 \
    python3-pip \
    && rm -rf /var/lib/apt/lists/*

RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
    && rm -f /etc/apache2/mods-enabled/mpm_*.conf \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

RUN a2enmod rewrite php8.1

RUN pip3 install pymupdf

COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY public/ /var/www/html/

RUN rm -f /var/www/html/index.html \
    && mkdir -p /var/www/html/uploads /var/www/html/output \
    && chown -R www-data:www-data /var/www/html \
    && chmod 777 /var/www/html/uploads /var/www/html/output

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]