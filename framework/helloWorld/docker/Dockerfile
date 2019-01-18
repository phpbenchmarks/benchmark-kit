FROM phpbenchmarks/benchmark-kit:latest

# add benchmark commands in sudoers
COPY .phpbenchmarks/sudoers /etc/sudoers.d/component
RUN chmod 440 /etc/sudoers.d/component

# copy config files
COPY .phpbenchmarks/configuration.sh /var/phpbenchmarks/configuration.sh
COPY .phpbenchmarks/responseBody.txt /var/phpbenchmarks/responseBody.txt
COPY .phpbenchmarks/vhost.conf /var/phpbenchmarks/vhost.conf

# create user with same name as host user name
#ARG DOCKER_UID
#RUN useradd -ms /bin/bash --uid ${DOCKER_UID} phpbenchmarks \
#    && usermod -a -G www-data phpbenchmarks

USER phpbenchmarks
ENTRYPOINT ["/var/phpbenchmarks/validate.sh"]
