Requirements
-

You will need this dependencies to make it work:
* Linux, to use Docker and bash scripts
* [Docker ^18.06](https://docs.docker.com/install/)
* [docker-compose ^1.12](https://docs.docker.com/compose/install/)

Installation
-

Add `phpbenchmarks/benchmark-kit` as dependency of your projet.

Not you have to do it in `require`, not in `require-dev`.
As benchmark kit contains only bash scripts, requiring it will not affect your code until you call them manually.

```bash
composer require phpbenchmarks/benchmark-kit ^1.0
```

[Back to documentation index](../README.md)
