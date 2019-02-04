<p align="center">
  <img src="http://www.phpbenchmarks.com/images/logo_github.png">
  <br>
  <a href="http://www.phpbenchmarks.com" target="_blank">www.phpbenchmarks.com</a>
</p>

Benchmark kit is a tool to add a framework or a template engine benchmark on [phpbenchmarks.com](http://www.phpbenchmarks.com).

Documentation
-

 * [Understand repositories and branches](documentation/repositoriesAndBranches.md)

Requirements
-

You will need this dependencies to make it work:
* Linux to use Docker and bash scripts
* [Docker ^18.06](https://docs.docker.com/install/)
* [docker-compose ^1.12](https://docs.docker.com/compose/install/)

Installation
-

```bash
# you can install it where you want, ~/benchmarkKit used for the example
mkdir ~/benchmarkKit
cd ~/benchmarkKit
echo '{"require": {"phpbenchmarks/benchmark-kit": "^2.0"}}' > composer.json

# you can use your local composer installation, of the official Docker container
docker run --rm -v $(pwd):/app composer/composer update --no-dev
# in Docker container, composer update is called with root user, so change permissions to current user
sudo chown -R $USER:$USER vendor
```

Benchmark kit commands
-

When you are inside benchmark kit Docker container, you can use `phpbench` to list benchmark kit commands.

Almost all commands accept this options:
* `--skip-branch-name`: don't validate git branch name, usefull while you are in development and repositories are not created yet.
* `--skip-source-code-urls`: don't validate source code urls, usefull while you are in development.
* `--validate-prod`: you should not need it, it's used when we test your code before benchmarking it.

```bash
phpbench benchmark:validate --skip-branch-name --skip-source-code-urls
```

#1 Ask us to create repositories
-

You can ask us to create repositories with [contact form](http://www.phpbenchmarks.com/en/contact?subject=create-benchmark-repositories).

Tell us which component and version you want to benchmark,
and `your github username` to allow you to commit on this repositories.

We will send you an email when repositories will be created.


#2 Start benchmark kit
-

To start benchmark kit Docker container, you have to call `./vendor/bin/start.sh`.

Do not start is as root, use your user.

It will ask you the directory where you code is located.
You can pass this directory as parameter to this script.

```bash
./vendor/bin/start.sh
./vendor/bin/start.sh /foo/code
```

#3 Initialize code
-

To make your benchmark work you will need some files into `.phpbenchmarks` directory:
* `AbstractComponentConfiguration.php`: configuration of benchmarked component.
* `initBenchmark.sh`: called before the benchmark to initialize everything (composer install, cache warmup etc).
* `vhost.conf`: nginx virtual host configuration.
* `responseBody/`: benchmark url body will be compared to files in this directory to validate it's content.

All this files can be created and configured with `phpbench` commands:

```
composer:update                     Execute composer update for all enabled PHP versions and create composer.lock.phpX.Y

configure:all                       Call all configure commands
configure:component                 Create .phpbenchmarks/AbstractComponentConfiguration.php and configure it
configure:component:sourceCodeUrls  Create .phpbenchmarks/AbstractComponentConfiguration.php and configure getSourceCodeUrls()
configure:directory                 Create .phpbenchmarks and .phpbenchmarks/responseBody directories
configure:initBenchmark             Create .phpbenchmarks/initBenchmark.sh
configure:responseBody              Create .phpbenchmarks/responseBody files
configure:vhost                     Create .phpbenchmarks/vhost.conf, create phpXY.benchmark.loc vhosts and reload nginx
```

You can call `configure:all` to create all of them, or use the one your need.

Note the `phpbench composer:update` command. We need a `composer.lock` per PHP version,
because some dependencies are installed in different versions depending on the version of PHP.
Use `phpbench composer:update` to switch between PHP version, and create `composer.lock.phpX.Y`.

```bash
phpbench composer:update
# you can specify a version of php
phpbench composer:update 7.1
```

#4 Add required features for benchmarks
-

Choose the component type and benchmark type you want to code:

* Framework
  * [Hello world benchmark](documentation/framework/helloWorld.md)
  * REST API benchmark (coming soon)
* Template engine
  * Hello world benchmark (coming soon)

Note that `all` component benchmarks needs to bo validated to make your component appear on [phpbenchmarks.com](http://www.phpbenchmarks.com).

#5 Test and validate your code
-

Docker container provide a domain for each PHP version, from 5.6 to 7.3:
* http://php56.benchmark.loc
* http://php70.benchmark.loc
* http://php71.benchmark.loc
* http://php72.benchmark.loc
* http://php73.benchmark.loc

You can use them to test your code.

When you think it's ok, use `phpbench benchmark:validate` to validate it.

#6 Submit your code
-

When `phpbench benchmark:validate` say it's good,
you can tell us to launch benchmarks with [contact form](http://www.phpbenchmarks.com/en/contact?subject=launch-benchmark).

Thank you!
