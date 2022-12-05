<?php

$config = require __DIR__.'/scripts/php-cs-fixer.php';

return $config(
    'contentful-core',
    true,
    ['scripts', 'src', 'tests']
);
