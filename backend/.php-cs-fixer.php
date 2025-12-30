<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src/AdminBundle',
        __DIR__ . '/src/AppBundle',
        __DIR__ . '/src/ApiBundle',
        __DIR__ . '/src/AppBundle',
        __DIR__ . '/src/InvoiceBundle',
        __DIR__ . '/src/UserBundle',
    ])
    ->name('*.php')
    ->exclude(['Tests']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'yoda_style' => [
            'equal' => true,
            'identical' => true,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder);
