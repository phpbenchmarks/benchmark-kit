function echo_git_branch {
    branch=$(git branch --no-color 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/(\1)/' -e 's/(//g' -e 's/)//g')
    if [ "$branch" != "" ]; then
        echo -en "[\e[33m$branch\e[00m]";
    fi
}
PS1='$(for (( i=0; i<$COLUMNS; i++ )); do echo -en "\033[35m_\033[00m"; done;)\n[\033[35m\u\033[00m] [\033[32m\w\033[00m] $(echo_git_branch) \n\$ '

alias phpbench="sudo /usr/bin/update-alternatives --set php /usr/bin/php7.3 && if [ ! -f /var/phpbenchmarks/vendor/autoload.php ]; then cd /var/phpbenchmarks && composer install --no-dev; fi && /var/phpbenchmarks/console"

alias php56="sudo /usr/bin/update-alternatives --set php /usr/bin/php5.6 ; php -v"
alias php70="sudo /usr/bin/update-alternatives --set php /usr/bin/php7.0 ; php -v"
alias php71="sudo /usr/bin/update-alternatives --set php /usr/bin/php7.1 ; php -v"
alias php72="sudo /usr/bin/update-alternatives --set php /usr/bin/php7.2 ; php -v"
alias php73="sudo /usr/bin/update-alternatives --set php /usr/bin/php7.3 ; php -v"

cd /var/www/phpbenchmarks

clear
echo "------------------------------------------------------------"
echo "| Welcome to PHP Benchmarks benchmark kit Docker container |"
echo "------------------------------------------------------------"
echo -en "| Host files are in \e[32m/var/www/phpbenchmarks\e[00m                 |\n"
echo -en "| Benchmark kit files are in \e[32m/var/phpbenchmarks\e[00m            |\n"
echo "|                                                          |"
echo "| You can test your code for each PHP version:             |"
echo "|   http://php56.benchmark.loc:$NGINX_PORT                        |"
echo "|   http://php70.benchmark.loc:$NGINX_PORT                        |"
echo "|   http://php71.benchmark.loc:$NGINX_PORT                        |"
echo "|   http://php72.benchmark.loc:$NGINX_PORT                        |"
echo "|   http://php73.benchmark.loc:$NGINX_PORT                        |"
echo "|                                                          |"
echo -en "| Use \e[33mphpbench\e[00m command to list available commands.         |\n"
echo "------------------------------------------------------------"
