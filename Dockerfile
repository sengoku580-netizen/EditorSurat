# ─── Base: PHP 8.2 + Apache ───────────────────────────────
FROM php:8.2-apache

# ─── Fix: disable mpm_event, enable mpm_prefork ──────────
RUN a2dismod mpm_event || true \
    && a2enmod mpm_prefork

# ─── Install Python3 + pip ────────────────────────────────
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    && rm -rf /var/lib/apt/lists/*

# ─── Install PyMuPDF (fitz) ───────────────────────────────
RUN pip3 install pymupdf --break-system-packages

# ─── Enable Apache mod_rewrite ───────────────────────────
RUN a2enmod rewrite

# ─── Copy Apache config ───────────────────────────────────
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# ─── Copy semua file public ke web root ──────────────────
COPY public/ /var/www/html/

# ─── Buat folder uploads & output, set permission ────────
RUN mkdir -p /var/www/html/uploads /var/www/html/output \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 777 /var/www/html/uploads /var/www/html/output

# ─── Expose port 80 ──────────────────────────────────────
EXPOSE 80