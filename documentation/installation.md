Requirements
-

You will need this dependencies to make it work:
* Linux to use Docker and bash scripts
* [Docker ^18.06](https://docs.docker.com/install/)
* [docker-compose ^1.12](https://docs.docker.com/compose/install/)

Installation
-

```bash
# you can install it where you want, ~/benchmarkKit used for the example
mkdir ~/benchmarkKit
cd ~/benchmarkKit
echo '{"require": {"phpbenchmarks/benchmark-kit": "^2.0"}}' > composer.json

# you can use your local composer installation, of the official Docker container
docker run --rm -v $(pwd):/app composer/composer update --no-dev
# in Docker container, composer update is called with root user, so change permissions to current user
sudo chown -R $USER:$USER vendor
```

[Back to documentation index](../README.md)
