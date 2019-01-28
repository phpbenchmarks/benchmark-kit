FROM phpbenchmarks/benchmark-kit:1.0.1

COPY entrypoint.sh /var/phpbenchmarks
RUN chmod +x /var/phpbenchmarks/entrypoint.sh
COPY common.sh /var/phpbenchmarks

# create user with same UID as host user name
# i don't understand how to use ARG in dockerhub images, so let's do this here
ARG DOCKER_UID
RUN useradd -ms /bin/bash --uid ${DOCKER_UID} phpbenchmarks \
    && usermod -a -G www-data phpbenchmarks

COPY componentFiles/.phpbenchmarks/configuration.sh /var/phpbenchmarks/componentFiles/.phpbenchmarks/

RUN chown -R phpbenchmarks:phpbenchmarks /var/phpbenchmarks

USER phpbenchmarks
WORKDIR /var/www/phpbenchmarks

ENTRYPOINT /var/phpbenchmarks/entrypoint.sh -v
