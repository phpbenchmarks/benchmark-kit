FROM phpbenchmarks/benchmark-kit:latest

# copy config files
COPY componentFiles/.phpbenchmarks/configuration.sh /var/phpbenchmarks
COPY componentFiles/.phpbenchmarks/vhost.conf /var/phpbenchmarks
COPY componentFiles/.phpbenchmarks/responseBody/* /var/phpbenchmarks/responseBody/

# create user with same UID as host user name
# i don't understand how to use ARG in dockerhub images, so let's do this here
ARG DOCKER_UID
RUN useradd -ms /bin/bash --uid ${DOCKER_UID} phpbenchmarks \
    && usermod -a -G www-data phpbenchmarks

COPY docker/validate/entrypoint.sh /var/phpbenchmarks
RUN chmod +x /var/phpbenchmarks/entrypoint.sh

COPY docker/validate/common.sh /var/phpbenchmarks

RUN chown -R phpbenchmarks:phpbenchmarks /var/phpbenchmarks

USER phpbenchmarks
ENTRYPOINT ["/var/phpbenchmarks/entrypoint.sh", "-v"]
