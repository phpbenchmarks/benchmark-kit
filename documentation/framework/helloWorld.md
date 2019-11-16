Hello world benchmark
-

This benchmark shows the overhead cost of using a framework instead of writing your code in PHP.

To respect that code should write `Hello World !` (yes, with space before `!`, cocorico ;)) in response body, as fast as possible.

Disable everything you can: template engine, session, database access etc.

Features
-

Don't forget this features as to be coded in [common repository](../repositoriesAndBranches.md).

* A route with the url `/benchmark/helloworld`. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_hello-world/Resources/config/routing.yml).
* A controller called by this route. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_hello-world/Controller/HelloWorldController.php).
* This controller should write `Hello World !` in response body as fast as possible. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_hello-world/Controller/HelloWorldController.php#L13).

[Back to documentation index](../../README.md)
