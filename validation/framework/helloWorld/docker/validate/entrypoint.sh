#!/usr/bin/env bash

source /var/phpbenchmarks/validate.sh

validateComposerJson
validateComposerLock

callInitBenchmark
validateBenchmarkUrlBodies
