<?php
require_once __DIR__ . '/../vendor/autoload.php';

\VCR\VCR::configure()
    ->setCassettePath('test/fixtures/vcr')
    ->enableLibraryHooks(['soap', 'curl'])
;
\VCR\VCR::turnOn();

define('FIXTURE_DIR', realpath(__DIR__ . '/fixtures'));