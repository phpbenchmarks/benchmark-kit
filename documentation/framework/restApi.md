REST API benchmark
-

This benchmark respresent a REST API application.

It contains 500 routes, 15,000 translations and return 100 PHP objects serialized in JSON.

An event is triggered to randomly define language and benchmark event dispatcher.

No database access is made, to not influence the results.

Features
-

Don't forget this features as to be coded in [common repository](../repositoriesAndBranches.md).

* A route with the url `benchmark/rest`. Is should be the first defined route. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_rest-api/Resources/config/routing.yml).
* 500 other routes with the url `/benchmark/test-route-x`, defined after the first one. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_rest-api/Resources/config/routing.yml).
* A controller called by the url `benchmark/rest`. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_rest-api/Controller/RestApiController.php).
  * Trigger an event to randomly define language. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_rest-api/Controller/RestApiController.php).
  * This event should randomly define language between `fr_FR`, `en_GB` and `en` (to use fallback system). [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_rest-api/EventListener/DefineLocaleEventListener.php).
  * Serialize the return of `PhpBenchmarksRestData\Service\Service::getUsers()` into json. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_rest-api/Normalizer/UserNormalizer.php).
  * Return this json in response body with `Content-Type: application/json` header. [Example](https://github.com/phpbenchmarks/symfony-common/blob/symfony_4_rest-api/Controller/RestApiController.php).
* 5,000 translations by language (`fr_FR`, `en_GB` and `en` so 15,000 translations in total). [Example](https://github.com/phpbenchmarks/laravel-common/blob/laravel_5_rest-api/Resources/lang/en_GB/phpbenchmarks.php).

[Back to documentation index](../../README.md)
