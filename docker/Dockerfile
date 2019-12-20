# This container should represent the benchmark server who is not dockerised
# So everything is installed into one container
FROM ubuntu:18.04

# Update and instal some dependencies
RUN \
    apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y \
        vim \
        zip \
        bash-completion \
        sudo \
        nginx \
        wget

# PHP
RUN \
    apt-get install -y software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt-get install -y \
        php5.6-fpm php5.6-xml php5.6-intl php5.6-mysql php5.6-mbstring php5.6-curl php5.6-zip php5.6-apcu \
        php7.0-fpm php7.0-xml php7.0-intl php7.0-mysql php7.0-mbstring php7.0-curl php7.0-zip php7.0-apcu \
        php7.1-fpm php7.1-xml php7.1-intl php7.1-mysql php7.1-mbstring php7.1-curl php7.1-zip php7.1-apcu \
        php7.2-fpm php7.2-xml php7.2-intl php7.2-mysql php7.2-mbstring php7.2-curl php7.2-zip php7.2-apcu \
        php7.3-fpm php7.3-xml php7.3-intl php7.3-mysql php7.3-mbstring php7.3-curl php7.3-zip php7.3-apcu \
        php7.4-fpm php7.4-xml php7.4-intl php7.4-mysql php7.4-mbstring php7.4-curl php7.4-zip php7.4-apcu

# Fix update-alternatives warning
RUN \
    touch /usr/share/man/man1/php5.6.1.gz \
    && touch /usr/share/man/man1/php7.0.1.gz \
    && touch /usr/share/man/man1/php7.1.1.gz \
    && touch /usr/share/man/man1/php7.2.1.gz \
    && touch /usr/share/man/man1/php7.3.1.gz \
    && touch /usr/share/man/man1/php7.4.1.gz

# Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Install git alone, or it will ask some geographical configs, I don't know why
RUN apt-get install -y git

# Add commands to sudoers
RUN \
    echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php5.6" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.0" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.1" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.2" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.3" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/bin/update-alternatives --set php /usr/bin/php7.4" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service nginx reload" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php5.6-fpm restart" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.0-fpm restart" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.1-fpm restart" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.2-fpm restart" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.3-fpm restart" >> /etc/sudoers.d/phpbenchmarks \
    && echo "phpbenchmarks ALL=(ALL) NOPASSWD: /usr/sbin/service php7.4-fpm restart" >> /etc/sudoers.d/phpbenchmarks

# We have some commands to do in this directory with phpbenchmarks user.
# So instead of adding a lot of commands into sudoers, and as security doesn't matter in this container,
# change this directory mod to 777
RUN \
    chmod -R 777 \
        /etc/nginx/sites-enabled \
        /var/log/nginx \
        /etc/php/5.6/fpm/conf.d \
        /etc/php/7.0/fpm/conf.d \
        /etc/php/7.1/fpm/conf.d \
        /etc/php/7.2/fpm/conf.d \
        /etc/php/7.3/fpm/conf.d \
        /etc/php/7.4/fpm/conf.d

# Create phpbenchmarks user, we assume final host user id will be 1000 (should be it 99% of the time)
RUN \
    useradd -ms /bin/bash --uid 1000 phpbenchmarks \
    && usermod -a -G www-data phpbenchmarks

# Use phpbenchmarks user for nginx and php-fpm to avoid file permission errors
RUN \
    sed -i 's/user www-data;/user phpbenchmarks;/g' /etc/nginx/nginx.conf \
    && sed -i 's/user = www-data/user = phpbenchmarks/g' /etc/php/5.6/fpm/pool.d/www.conf \
    && sed -i 's/group = www-data/group = phpbenchmarks/g' /etc/php/5.6/fpm/pool.d/www.conf \
    && sed -i 's/user = www-data/user = phpbenchmarks/g' /etc/php/7.0/fpm/pool.d/www.conf \
    && sed -i 's/group = www-data/group = phpbenchmarks/g' /etc/php/7.0/fpm/pool.d/www.conf \
    && sed -i 's/user = www-data/user = phpbenchmarks/g' /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i 's/group = www-data/group = phpbenchmarks/g' /etc/php/7.1/fpm/pool.d/www.conf \
    && sed -i 's/user = www-data/user = phpbenchmarks/g' /etc/php/7.2/fpm/pool.d/www.conf \
    && sed -i 's/group = www-data/group = phpbenchmarks/g' /etc/php/7.2/fpm/pool.d/www.conf \
    && sed -i 's/user = www-data/user = phpbenchmarks/g' /etc/php/7.3/fpm/pool.d/www.conf \
    && sed -i 's/group = www-data/group = phpbenchmarks/g' /etc/php/7.3/fpm/pool.d/www.conf \
    && sed -i 's/user = www-data/user = phpbenchmarks/g' /etc/php/7.4/fpm/pool.d/www.conf \
    && sed -i 's/group = www-data/group = phpbenchmarks/g' /etc/php/7.4/fpm/pool.d/www.conf

# Define PHP 7.4 as default
RUN /usr/bin/update-alternatives --set php /usr/bin/php7.4

# Copy benchmark kit source code
COPY benchmark/statistics.php /var/benchmark-kit/benchmark/statistics.php
COPY bin/console /var/benchmark-kit/bin/console
COPY config /var/benchmark-kit/config
COPY documentation /var/benchmark-kit/documentation
COPY src /var/benchmark-kit/src
COPY templates /var/benchmark-kit/templates
COPY vendor /var/benchmark-kit/vendor
COPY .env /var/benchmark-kit/.env
COPY changelog.md /var/benchmark-kit/changelog.md
COPY README.md /var/benchmark-kit/README.md
COPY phpbenchkit.sh /var/benchmark-kit/phpbenchkit.sh
RUN \
    mkdir /var/benchmark-kit/var \
    && chmod -R 777 /var/benchmark-kit/var

# Configure nginx default page
COPY docker/html /var/www/html
COPY docker/nginx/default /etc/nginx/sites-available
COPY docker/nginx/phpinfo.benchmark-kit.loc.conf /etc/nginx/sites-available
RUN rm /var/www/html/index.nginx-debian.html

# Run
WORKDIR /var/benchmark-kit
COPY --chown=phpbenchmarks docker/entrypoint.sh /var/entrypoint.sh
ENTRYPOINT ["/var/entrypoint.sh"]
