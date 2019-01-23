Requirements
-

You will need this dependencies to make it work:
* Linux, to use Docker and bash scripts
* [Docker ^18.06](https://docs.docker.com/install/)
* [docker-compose ^1.12](https://docs.docker.com/compose/install/)

Installation
-

```bash
mkdir ~/benchmarkKit
cd ~/benchmarkKit
echo '{"require": {"phpbenchmarks/benchmark-kit": "^1.0"}}' > composer.json
docker run --rm -v $(pwd):/app composer/composer update
# composer update is called with root user, so change permissions to current user
sudo chown -R $USER:$USER vendor
```

[Back to documentation index](../README.md)
