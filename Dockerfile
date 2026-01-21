FROM php:8.2-cli

WORKDIR /app
COPY . /app

# Render uses $PORT automatically
CMD php -S 0.0.0.0:$PORT -t /app index.php
