# Используем базовый образ PHP с Apache
FROM php:8.1-apache

# Устанавливаем необходимые PHP-расширения
RUN docker-php-ext-install pdo pdo_mysql

# Копируем содержимое папки в директорию веб-сервера
COPY . /var/www/html/

# Устанавливаем права доступа
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Включаем модуль Apache для работы с .htaccess (если необходимо)
RUN a2enmod rewrite

# Указываем рабочую директорию
WORKDIR /var/www/html

# Открываем порт для работы
EXPOSE 80
