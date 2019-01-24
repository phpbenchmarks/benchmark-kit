Initialize branch
-

To make benchmark kit works, some file are required in your `main` repository branch.
<br>
You can use `initializeBranch.sh` to create them.
<br>
Feel free to edit them manually.

* `README.md`: explanations about phpbenchmarks.com, this repository etc. 
* `.phpbenchmarks/vhost.conf`: nginx configuration for virtual host.
* `.phpbenchmarks/configuration.sh`: configure enabled PHP versions, component version etc.
* `.phpbenchmarks/initBenchmark.sh`: should contains `initBenchmark()` function. Called before the benchmark to clear cache, install dependencies etc.
* `.phpbenchmarks/responseBody/`: should contains files to compare the body returned by your code to expected one.
* `.phpbenchmarks/codeLink.sh`: should contains `$codeLinks` associative array, which contains links to your code. Use [./codeLink.sh](codeLink.md) to easily edit it.

./initializeBranch.sh
-

Without any parameter, it will ask you 3 informations:
* component type (framework or templateEngine)
* benchmark type (hello-world or rest-api)
* path to your code

Available options:
* `-v`: view each validations performed
* `-vv`: view each validations performed and wget output if configuration files are downloaded from github

```bash
# will ask the 3 informations
./initializeBranch.sh
# first parameter is component type, it will ask only the 2 next informations
./initializeBranch.sh framework
# second parameter is benchmark type, it will ask only the path to your code
./initializeBranch.sh framework hello-world
# all informations are passed as parameters, no ask
./initializeBranch.sh framework hello-world /foo/bar
```

[Back to documentation index](../README.md)
