# ---------------------------------------------------
# Etapa 1: Compilación de recursos estáticos (Node)
# ---------------------------------------------------
FROM node:20 AS node-builder

WORKDIR /app
# Copiar archivos de Node.js
COPY package*.json ./
COPY vite.config.js ./
# Instalar dependencias
RUN npm ci
# Copiar TODO el proyecto para que Vite pueda escanear las vistas de Blade si es necesario
# y tenga acceso a cualquier asset que resida en public/
COPY . .
# Ejecutar compilación de Vite
RUN npm run build

# ---------------------------------------------------
# Etapa 2: Entorno PHP de Producción
# ---------------------------------------------------
FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Habilitar mod_rewrite de Apache para Laravel
RUN a2enmod rewrite

# Cambiar DocumentRoot de Apache a public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente de Laravel
COPY . .

# Copiar el build compilado desde la etapa de Node
COPY --from=node-builder /app/public/build ./public/build

# Instalar dependencias de Composer optimizadas para producción
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# IMPORTANTE: Eliminamos --no-scripts para permitir que Laravel ejecute 'package:discover'
# y reconozca todos los ServiceProviders de los paquetes correctamente.
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Aplicar permisos correctos a carpetas críticas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copiar el script de inicio y darle permisos de ejecución
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# El puerto estándar que expone Render/Apache
EXPOSE 80

# Iniciar el script que configura la DB y luego levanta Apache
CMD ["/usr/local/bin/start.sh"]
