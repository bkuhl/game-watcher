FROM nginx:1.11-alpine
# Copy the application public files to the container
ADD public /var/www/html/public
ADD infrastructure/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

RUN chown -R nginx:nginx public/
