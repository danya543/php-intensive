FROM php:8.2-apache

# Копируем файлы проекта в контейнер
COPY . /var/www/html/

# Открываем порт 80
EXPOSE 80

CMD ["apache2-foreground"]