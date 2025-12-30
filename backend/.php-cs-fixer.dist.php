<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->name('*.php')
    ->exclude(['Tests'])
    ->depth('>= 1')
    ->filter(function (\SplFileInfo $file): bool {
        return str_contains($file->getRealPath(), DIRECTORY_SEPARATOR . 'Bundle' . DIRECTORY_SEPARATOR);
    });

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2' => true,
    ])
    ->setFinder($finder);
