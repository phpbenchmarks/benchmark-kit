Use composerUpdate.sh and not your composer
-

While you are in development, you can use your own installation of Composer, or the official Composer Docker container (`docker run --rm -v $(pwd):/app composer/composer update`).

Some dependencies could be installed in different versions depending on the version of PHP.
<br>
To test your code with `./codeValidation.sh`,
you should have a `composer.lock` by enabled PHP version (configured in `.phpbenchmarks/configuration.sh`).
<br>
To create them, you can use `./composerUpdate.sh`, who will do a `composer update` into a Docker container then move `composer.lock` to `composer.lock.phpX.Y`.

./composerUpdate.sh
-

Without any parameter, it will ask you 3 informations:
* component type (framework or templateEngine)
* benchmark type (hello-world or rest-api)
* path to your code

Available options:
* `-v`: view each validations performed
* `-vv`: view each validations performed + docker-compose build details
* `--repositories-not-created`: some validations could not be done when when working before repositories are created. Use this parameter before repositories are created.
* `--prod`: validate everything is on the final branch and versioned, instead of development branch and not versioned.

```bash
cd vendor/phpbenchmarks/benchmark-kit

# will ask the 3 informations
./composerUpdate.sh
# first parameter is component type, it will ask only the 2 next informations
./composerUpdate.sh framework
# second parameter is benchmark type, it will ask only the path to your code
./composerUpdate.sh framework hello-world
# all informations are passed as parameters, no ask
./composerUpdate.sh framework hello-world /foo/bar
```

[Back to documentation index](../README.md)
