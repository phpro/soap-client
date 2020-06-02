<?php

use VCR\VCR;

require_once __DIR__.'/../vendor/autoload.php';

define('FIXTURE_DIR', realpath(__DIR__ . '/fixtures'));
define('VCR_CASSETTE_DIR', realpath(__DIR__ . '/fixtures/vcr'));

\VCR\VCR::configure()
    ->setCassettePath(VCR_CASSETTE_DIR)
    ->enableLibraryHooks(['soap', 'curl']);
VCR::turnOn();
