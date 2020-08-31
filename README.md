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
sudo wget https://raw.githubusercontent.com/phpbenchmarks/benchmark-kit/master/phpbenchkit.sh -O /usr/local/bin/phpbenchkit
sudo chmod +x /usr/local/bin/phpbenchkit
```

Then call `phpbenchkit`, it will add vhosts and run Docker benchmatk kit container:
```bash
# Add vhosts if needed, run Docker container and call bin/console to show available commands
phpbenchkit

# Restart Docker container
phpbenchkit --restart

# Stop Docker container
phpbenchkit --stop

# Update docker image and phpbenchkit command
phpbenchkit --selfupdate
```

## Benchmark kit commands

List available commands:
```bash
phpbenchkit
```

Almost all commands accept this option:
* `--skip-source-code-urls`: don't validate source code urls, usefull while you are in development.

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

All this files can be created and configured with `configure` commands.

See list of configure commands with `phpbenchkit configure:`.

You can call `phpbenchkit configure:benchmark` to create all of them or use the one your need.

```bash
phpbenchkit composer:update
# you can specify a version of php
phpbenchkit composer:update 7.1
```

You can validate each part of your configuration with `validate`.

See list of validation commands with with `phpbenchkit validate:`.

## #4 Add required feaures for benchmarks

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

When you think it's ok, use `phpbenchkit validate:benchmark` to validate it.

## #6 Submit your code

When `phpbenchkit validate:benchmark` say it's good, push your code,
then you can tell us to launch benchmarks with [contact form](http://www.phpbenchmarks.com/en/contact?subject=launch-benchmark).

Thank you!
