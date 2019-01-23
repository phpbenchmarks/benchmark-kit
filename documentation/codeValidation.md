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

Without any parameter, it will ask you 3 informations:
* component type (framework or templateEngine)
* benchmark type (hello-world or rest-api)
* path to your code

Available options:
* `-v`: view each validations performed
* `-vv`: view each validations performed + docker-compose build details
* `--repositories-not-created`: some validations could not be done when working locally, use this parameter before repositories are created.
* `--prod`: validate everything is on the final branch and versioned, instead of development branch and not versioned.

```bash
# will ask the 3 informations
./codeValidation.sh
# first parameter is component type, it will ask only the 2 next informations
./codeValidation.sh framework
# second parameter is benchmark type, it will ask only the path to your code
./codeValidation.sh framework hello-world
# all informations are passed as parameters, no ask
./codeValidation.sh framework hello-world /foo/bar
```

[Back to documentation index](../README.md)
