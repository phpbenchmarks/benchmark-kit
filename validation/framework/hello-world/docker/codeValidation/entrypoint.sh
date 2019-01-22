#!/usr/bin/env bash

source /var/phpbenchmarks/codeValidation.sh

validateComposerJson
validateComposerLock

callInitBenchmark
validateBenchmarkUrlBodies
