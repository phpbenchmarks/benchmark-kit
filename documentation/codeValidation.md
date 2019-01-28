Validate your code
-

To validate your code (configuration and benchmark url), you can use `./codeValidation.sh`.

What will be validated:
* `.phpbenchmarks` directory, and all it's configuration files
* `README.md` content
* git branch name
* `composer.json` and `composer.lock.phpX.Y` dependencies
* benchmark url with each enabled PHP version

./codeValidation.sh
-

Without any parameter, it will ask you 2 informations:
* component type (framework or templateEngine)
* benchmark type (hello-world or rest-api)

Available options:
* `-v`: view each validations performed
* `-vv`: view each validations performed + docker-compose build details
* `--skip-branch-name`: some validations could not be done when working before repositories are created. Use this parameter before repositories are created.
* `--prod`: validate everything is on the final branch and versioned, instead of development branch and not versioned.

```bash
cd vendor/phpbenchmarks/benchmark-kit

# will ask the 3 informations
./codeValidation.sh
# first parameter is component type, it will ask only the 2 next informations
./codeValidation.sh framework
# all informations are passed as parameters, no ask
./codeValidation.sh framework hello-world
```

[Back to documentation index](../README.md)
