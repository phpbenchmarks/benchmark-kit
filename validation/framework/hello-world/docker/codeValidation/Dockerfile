FROM phpbenchmarks/benchmark-kit:1.0.1

# copy config files
COPY componentFiles/.phpbenchmarks /var/phpbenchmarks/componentFiles/.phpbenchmarks/

# create user with same UID as host user name
# i don't understand how to use ARG in dockerhub images, so let's do this here
ARG DOCKER_UID
RUN useradd -ms /bin/bash --uid ${DOCKER_UID} phpbenchmarks \
    && usermod -a -G www-data phpbenchmarks

COPY docker/codeValidation/entrypoint.sh /var/phpbenchmarks
RUN chmod +x /var/phpbenchmarks/entrypoint.sh

COPY docker/codeValidation/common.sh /var/phpbenchmarks

RUN chown -R phpbenchmarks:phpbenchmarks /var/phpbenchmarks

USER phpbenchmarks
ENTRYPOINT /var/phpbenchmarks/entrypoint.sh -v
