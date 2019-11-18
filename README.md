<p align="center">
  <img src="http://www.phpbenchmarks.com/images/logo_github.png">
  <br>
  <a href="http://www.phpbenchmarks.com" target="_blank">www.phpbenchmarks.com</a>
</p>

Benchmark kit is a tool to add a framework or a template engine benchmark on [phpbenchmarks.com](http://www.phpbenchmarks.com).

## Documentation

 * [Changelog](changelog.md)
 * [Understand repositories and branches](documentation/repositoriesAndBranches.md)

## Requirements

You will need [Docker ^18.06](https://docs.docker.com/install/) to make it work.

## Installation

Everything you need to use benchmark kit is is [hpbenchkit.sh](phpbenchkit.sh).

Install it as global bin and give it execute mode:
```bash
sudo wget https://raw.githubusercontent.com/phpbenchmarks/benchmark-kit/4.0.0/phpbenchkit.sh -O /usr/local/bin/phpbenchkit
sudo chmod +x /usr/local/bin/phpbenchkit
phpbenchkit --selfupdate
```

Then call `phpbenchkit`, it will add vhosts and run Docker benchmatk kit container:
```bash
# Add vhosts if needed, run Docker container and call bin/console to show available commands
phpbenchkit

# Restart Docker container
phpbenchkit --restart

# Stop Docker container
phpbenchkit --stop

# Update docker image and phpbenchkit command
phpbenchkit --selfupdate
```

## Benchmark kit commands

List available commands:
```bash
phpbenchkit
```

Almost all commands accept this options:
* `--skip-branch-name`: don't validate git branch name, usefull while you are in development and repositories are not created yet.
* `--skip-source-code-urls`: don't validate source code urls, usefull while you are in development.
* `--validate-prod`: you should not need it, it's used when we test your code before benchmarking it.

## #1 Ask us to create repositories

You can ask us to create repositories with [contact form](http://www.phpbenchmarks.com/en/contact?subject=create-benchmark-repositories).

Tell us which component and version you want to benchmark,
and `your github username` to allow you to commit on this repositories.

We will send you an email when repositories will be created.

## #2 Initialize code

To make your benchmark work you will need some files into `.phpbenchmarks` directory:
* `Configuration.php`: configuration of benchmarked component.
* `initBenchmark.sh`: called before the benchmark to initialize everything (composer install, cache warmup etc).
* `vhost.conf`: nginx virtual host configuration.
* `responseBody/`: benchmark url body will be compared to files in this directory to validate it's content.
* `composer/composer.lock.phpX.Y`: created by `phpbenchkit composer:update` to install dependencies by PHP version.

All this files can be created and configured with `phpbenchkit` commands:

```
composer
    composer:update                                 Execute composer update for all enabled PHP versions and create .phpbenchmarks/composer/composer.lock.phpX.Y
configure
    configure:all                                   Call all configure commands and composer:update
    configure:configuration-class                   Create .phpbenchmarks/Configuration.php and configure it
    configure:configuration-class:source-code-urls  Create .phpbenchmarks/Configuration.php and configure getSourceCodeUrls()
    configure:directory                             Create .phpbenchmarks directory and subdirectories
    configure:initBenchmark                         Create .phpbenchmarks/initBenchmark.sh
    configure:readme                                Create README.md
    configure:response-body                         Create .phpbenchmarks/responseBody files
    configure:vhost                                 Create .phpbenchmarks/vhost.conf
```

You can call `configure:all` to create all of them or use the one your need.

```bash
phpbench composer:update
# you can specify a version of php
phpbench composer:update 7.1
```

You can validate each part of your configuration with `phpbench` commands:
```
validate:all                                     Call all validate commands
validate:branch:name                             Validate branch name: component_X.Y_benchmark-type_prepare
validate:composer:json                           Validate dependencies in composer.json
validate:composer:lock                           Validate dependencies in .phpbenchmarks/composer/composer.lock.phpX.Y
validate:configuration:class                     Validate .phpbenchmarks/Configuration.php
validate:configuration:class:sourceCodeUrls      Validate .phpbenchmarks/Configuration.php::getSourceCodeUrls()
validate:configuration:initBenchmark             Validate .phpbenchmarks/initBenchmark.sh
validate:configuration:responseBody              Validate .phpbenchmarks/responseBody files
validate:configuration:vhost                     Validate .phpbenchmarks/vhost.conf
```

## #4 Add required feaures for benchmarks
-

Choose the component type and benchmark type you want to code:

* Framework
  * [Hello world benchmark](documentation/framework/helloWorld.md)
  * [REST API benchmark](documentation/framework/restApi.md)
* Template engine
  * [Hello world benchmark](documentation/templateEngine/helloWorld.md)

Note that `all` component benchmarks needs to bo validated to make your component appear on [phpbenchmarks.com](http://www.phpbenchmarks.com).

## #5 Test and validate your code

Docker container provide a domain to test your code: `http://benchmark-kit.loc`.

To change PHP version (CLI and FPM):
```bash
phpbenchkit 
```

When you think it's ok, use `phpbench benchmark:validate` to validate it.

## #6 Submit your code

When `phpbench benchmark:validate` say it's good, push your code,
then you can tell us to launch benchmarks with [contact form](http://www.phpbenchmarks.com/en/contact?subject=launch-benchmark).

Thank you!
