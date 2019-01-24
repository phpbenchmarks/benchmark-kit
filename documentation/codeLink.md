Configure code links
-

To prove all features are coded, you need to configure `$codeLinks` in `.phpbenchmarks/codeLink.sh`.

To do it easily, you can use `./codeLink.sh`.

./codeLink.sh
-

Without any parameter, it will ask you 3 informations:
* component type (framework or templateEngine)
* benchmark type (hello-world or rest-api)
* path to your code

```bash
cd vendor/phpbenchmarks/benchmark-kit

# will ask the 3 informations
./codeLink.sh
# first parameter is component type, it will ask only the 2 next informations
./codeLink.sh framework
# second parameter is benchmark type, it will ask only the path to your code
./codeLink.sh framework hello-world
# all informations are passed as parameters, no ask
./codeLink.sh framework hello-world /foo/bar
```

[Back to documentation index](../README.md)
