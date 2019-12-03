<?php

$data = memory_get_usage() . "\n"
    . memory_get_peak_usage() . "\n"
    . memory_get_usage(true) . "\n"
    . memory_get_peak_usage(true) . "\n"
    . count(get_declared_classes()) . "\n"
    . count(get_declared_interfaces()) . "\n"
    . count(get_declared_traits()) . "\n"
    . count(get_defined_functions()) . "\n"
    . count(get_defined_constants()) . "\n";

file_put_contents('/tmp/phpbenchmarks-stats-php', $data);
