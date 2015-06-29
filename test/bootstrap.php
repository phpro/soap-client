<?php
require_once __DIR__ . '/../vendor/autoload.php';

\VCR\VCR::configure()
    ->setCassettePath('test/fixtures')
    ->enableLibraryHooks(['soap'])
;
\VCR\VCR::turnOn();
