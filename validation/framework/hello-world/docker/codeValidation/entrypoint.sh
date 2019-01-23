#!/usr/bin/env bash

source /var/phpbenchmarks/codeValidation.sh

validateComposerJson
validateComposerLock

function validateBenchmarkBody {
    local phpVersion=$1

    echoBlock 45 "PHP $phpVersion"
    callInitBenchmark
    validateBody $phpVersion $PHPBENCHMARKS_BENCHMARK_URL
}

[ "$PHPBENCHMARKS_PHP_5_6_ENABLED" == "true" ] && validateBenchmarkBody "5.6" "$PHPBENCHMARKS_BENCHMARK_URL"
[ "$PHPBENCHMARKS_PHP_7_0_ENABLED" == "true" ] && validateBenchmarkBody "7.0" "$PHPBENCHMARKS_BENCHMARK_URL"
[ "$PHPBENCHMARKS_PHP_7_1_ENABLED" == "true" ] && validateBenchmarkBody "7.1" "$PHPBENCHMARKS_BENCHMARK_URL"
[ "$PHPBENCHMARKS_PHP_7_2_ENABLED" == "true" ] && validateBenchmarkBody "7.2" "$PHPBENCHMARKS_BENCHMARK_URL"
[ "$PHPBENCHMARKS_PHP_7_3_ENABLED" == "true" ] && validateBenchmarkBody "7.3" "$PHPBENCHMARKS_BENCHMARK_URL"
