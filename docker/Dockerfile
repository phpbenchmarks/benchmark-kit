FROM ubuntu:18.04

RUN apt-get update \
    && apt-get upgrade -y
RUN apt-get install -y \
    vim \
    zip \
    bash-completion \
    sudo \
    nginx \
    wget

# php
RUN apt-get install -y software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt-get install -y \
        php5.6-fpm php5.6-xml php5.6-intl php5.6-mysql php5.6-mbstring php5.6-curl php5.6-zip php5.6-apcu \
        php7.0-fpm php7.0-xml php7.0-intl php7.0-mysql php7.0-mbstring php7.0-curl php7.0-zip php7.0-apcu \
        php7.1-fpm php7.1-xml php7.1-intl php7.1-mysql php7.1-mbstring php7.1-curl php7.1-zip php7.1-apcu \
        php7.2-fpm php7.2-xml php7.2-intl php7.2-mysql php7.2-mbstring php7.2-curl php7.2-zip php7.2-apcu \
        php7.3-fpm php7.3-xml php7.3-intl php7.3-mysql php7.3-mbstring php7.3-curl php7.3-zip php7.3-apcu

# composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && mv composer.phar /usr/bin/composer \
    && chmod +x /usr/bin/composer \
    && php -r "unlink('composer-setup.php');"

# install git alone, or it will ask some geographical configs, I don't know why
RUN apt-get install -y git

# add commands to sudoers
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service nginx start" > /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php5.6-fpm start" >> /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.0-fpm start" >> /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.1-fpm start" >> /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.2-fpm start" >> /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.3-fpm start" >> /etc/sudoers.d/phpbenchmarks

RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php5.6" >> /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.0" >> /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.1" >> /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.2" >> /etc/sudoers.d/phpbenchmarks
RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.3" >> /etc/sudoers.d/phpbenchmarks

RUN echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service nginx reload" >> /etc/sudoers.d/phpbenchmarks

# we have some commands to do in this dir with phpbenchmarks user
# so instead of adding a lot of commands into sudoers, and as security doesn't matter in this container,
# change this dir credentials to 777
RUN chmod 777 /etc/nginx/sites-enabled

# fix accents problems (https://github.com/tianon/docker-brew-debian/issues/45)
RUN apt-get install locales
RUN echo "LC_ALL=fr_FR.UTF-8" >> /etc/environment
RUN echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen
RUN echo "LANG=fr_FR.UTF-8" > /etc/locale.conf
RUN locale-gen fr_FR.UTF-8

# create phpbenchmarks user
ARG DOCKER_UID
RUN useradd -ms /bin/bash --uid ${DOCKER_UID} phpbenchmarks \
    && usermod -a -G www-data phpbenchmarks

# bashrc
COPY docker/.bashrc /home/phpbenchmarks/.bashrc.docker
RUN echo "source /home/phpbenchmarks/.bashrc.docker" >> /home/phpbenchmarks/.bashrc

# fix update-alternatives warning
RUN \
    touch /usr/share/man/man1/php5.6.1.gz \
    touch /usr/share/man/man1/php7.0.1.gz \
    touch /usr/share/man/man1/php7.1.1.gz \
    touch /usr/share/man/man1/php7.2.1.gz \
    touch /usr/share/man/man1/php7.3.1.gz

CMD \
    /etc/init.d/php5.6-fpm start \
    && /etc/init.d/php7.0-fpm start \
    && /etc/init.d/php7.1-fpm start \
    && /etc/init.d/php7.2-fpm start \
    && /etc/init.d/php7.3-fpm start \
    && nginx -g "daemon off;"
