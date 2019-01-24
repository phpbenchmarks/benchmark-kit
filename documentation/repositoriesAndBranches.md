Understand repositories and branches
-

Each benchmarked component source code is divided in 2 repositories: a `main` repository and a `common` repository.

Main repository
-

Only component installation, with `prod` env configured.

One branch per minor version, and per benchmark.
<br>
Examples: `symfony_3.4_hello-world`, `symfony_4.2_rest-api`.

Features for benchmarks are not in this repository, they are in `common` repository.
<br>
This repository should have `common` repository in dependencies.

Main dependencies (framework components for example) should have full version (major.minor.bugfix), no `^` or `~` are allowed.
<br>
Other dependencies could use `^` or `~`.

Example of `composer.json`:
```json
{
    "name": "phpbenchmarks/symfony",
    "license": "proprietary",
    "type": "project",
    "require": {
        "php": "^7.1.3",
        "symfony/console": "4.2.2",
        "symfony/framework-bundle": "4.2.2",
        "symfony/yaml": "4.2.2",
        "symfony/flex": "^1.1",
        "phpbenchmarks/symfony-common": "4.1.1"
    }
}
```

[Example with Symfony main repository](https://github.com/phpbenchmarks/symfony/branches/all)

Common repository
-

To reuse code between minors versions of a component, this repository should only contains benchmark features.
<br>
Component installation files are not in this repository, they are in the `main` repository.
<br>
It could be a bundle, a plugin or something else, installed as dependency into `main` repository.

One branch per major version, and per benchmark.
<br>
Examples: `symfony_3_hello-world`, `symfony_4_rest-api`.

Code on a branch should be compatible with all minors versions of the component.
<br>
Example: `symfony_3_hello-world` branch code is compatible with Symfony 3.0, 3.1, 3.2, 3.3 and 3.4.

Example of `composer.json`:
```json
{
    "name": "phpbenchmarks/symfony-common",
    "license": "proprietary",
    "type": "project",
    "require": {
        "php": "^7.1.3",
        "symfony/console": "^4.0",
        "symfony/framework-bundle": "^4.0",
        "symfony/yaml": "^4.0",
        "symfony/flex": "^1.1"
    },
    "autoload": {
        "psr-4": {
            "PhpBenchmarksSymfony\\": ""
        }
    }
}
```

[Example with Symfony main repository](https://github.com/phpbenchmarks/symfony-common/branches/all)

Development phase
-

You will not be able to directly push into `main` and `common` repositories final branches.
<br>
You have to add `_prepare` suffix to branches, and when your code is finalized, create a pull request on final branches.
Example:
* `main` repository
   * branch `symfony_4.0_hello-world_prepare`
   * dependency to `"phpbenchmarks/symfony-common": "dev-symfony_4_hello-world_prepare"`
* `common` repository
   * branch `symfony_4_hello-world_prepare`

You will probably have to add `"minimum-stability": "dev"` and `"prefer-stable": true` to `main` repository composer.json,
to be able to have `dev-symfony_4_hello-world_prepare` as dependency.

[Back to documentation index](../README.md)
