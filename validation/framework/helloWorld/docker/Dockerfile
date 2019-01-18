FROM phpbenchmarks/benchmark-kit:latest

# add benchmark commands in sudoers
COPY componentFiles/.phpbenchmarks/sudoers /etc/sudoers.d/component
RUN chmod 440 /etc/sudoers.d/component

# copy config files
COPY componentFiles/.phpbenchmarks/configuration.sh /var/phpbenchmarks/configuration.sh
COPY componentFiles/.phpbenchmarks/responseBody.txt /var/phpbenchmarks/responseBody.txt
COPY componentFiles/.phpbenchmarks/vhost.conf /var/phpbenchmarks/vhost.conf

# create user with same UID as host user name
# i don't understand how to use ARG in dockerhub images, so let's do this here
ARG DOCKER_UID
RUN useradd -ms /bin/bash --uid ${DOCKER_UID} phpbenchmarks \
    && usermod -a -G www-data phpbenchmarks

COPY docker/validation/entrypoint.sh /var/phpbenchmarks/entrypoint.sh
RUN chmod +x /var/phpbenchmarks/entrypoint.sh

USER phpbenchmarks
ENTRYPOINT ["/var/phpbenchmarks/entrypoint.sh"]
