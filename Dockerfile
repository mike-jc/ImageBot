# escape=\

FROM php:5.6

MAINTAINER 24sessions

ENV DEBIAN_FRONTEND="noninteractive"

# install necessary stuff
RUN apt-get update \
    && apt-get install -y --allow-unauthenticated \
        git \
	    php5-gd \
	    rabbitmq-server \
        unzip \
        vim \
        zip \
    && docker-php-ext-install bcmath \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | \
       php -- --install-dir=/usr/local/bin --filename=composer \
    && echo 'extension=/usr/lib/php5/20131226/gd.so' >> /usr/local/etc/php/php.ini \
    && echo 'extension=bcmath.so' >> /usr/local/etc/php/php.ini

RUN git clone https://github.com/mike-jc/ImageBot.git /home/ImageBot \
    && cd /home/ImageBot \
    && php -d memory_limit=-1 /usr/local/bin/composer install --no-interaction --no-scripts

RUN chmod 755 /home/ImageBot/bin/bot \
    && ln -sv /home/ImageBot/bin/bot /usr/local/bin/bot

