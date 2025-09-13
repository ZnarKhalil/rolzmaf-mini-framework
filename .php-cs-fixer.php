<?php

declare(strict_types=1);

$header = <<<'HEADER'
Rolzmaf — PHP mini framework
(c) 2025 Znar Khalil
HEADER;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/core',
        __DIR__ . '/app',
        __DIR__ . '/tests',
        __DIR__ . '/public',
        __DIR__ . '/config',
    ])
    ->exclude(['vendor', 'storage', 'database', 'node_modules'])
    ->name('*.php')
    ->ignoreVCS(true);

return new PhpCsFixer\Config()
    ->setRiskyAllowed(true)
    ->setRules([
        // Base
        '@PSR12' => true,
        'declare_strict_types' => true,

        // Style preferences
        'array_syntax' => ['syntax' => 'short'],
        'single_quote' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],

        // Cleanups
        'no_superfluous_phpdoc_tags' => true,
        'phpdoc_trim' => true,
        'phpdoc_align' => ['align' => 'left'],

        // Readability / spacing
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'blank_line_before_statement' => ['statements' => ['return']],
        'no_whitespace_in_blank_line' => true,

        // Helpful "risky" but useful rules
        'simplified_null_return' => true,
        'void_return' => true,

        // Header
        'header_comment' => [
            'header' => $header,
            'comment_type' => 'PHPDoc',
            'location' => 'after_open',
            'separate' => 'both',
        ],
    ])
    ->setFinder($finder);