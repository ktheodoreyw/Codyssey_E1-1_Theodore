FROM php:8.2.23
RUN docker-php-ext-install pdo_mysql
RUN apt-get update
RUN apt-get install -y msmtp-mta
COPY ./msmtprc /etc/msmtprc
RUN chmod 644 /etc/msmtprc
RUN echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/mail.ini
CMD ["/usr/local/bin/php", "--server", "0.0.0.0:8000", "--docroot", "/my-work"]
