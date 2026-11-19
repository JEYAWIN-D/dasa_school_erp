<?php
/**
 * STAGE 7 UI, UX, RESPONSIVE, ACCESSIBILITY & CROSS-BROWSER AUDIT
 * DASA EduERP Application Suite (v2.6 Enterprise)
 */

declare(strict_types=1);

$viewsDir = dirname(__DIR__) . '/resources/views';
$qaDir = dirname(__DIR__) . '/QA_AUDIT/VERSION_1';

echo "====================================================================\n";
echo " DASA EduERP — STAGE 7 UI/UX & RESPONSIVE DESIGN AUDIT SUITE\n";
echo "====================================================================\n\n";

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$bladeFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $bladeFiles[] = $file->getPathname();
    }
}

echo "Total Blade Templates Discovered: " . count($bladeFiles) . "\n\n";

$findings = [
    'table_overflow_missing' => [],
    'alpine_bracket_leaks' => [],
    'hardcoded_urls' => [],
    'low_contrast_classes' => [],
    'unlabeled_inputs' => [],
    'unclosed_directives' => [],
];

$inspectedViewsCount = 0;
$totalTablesCount = 0;
$totalFormsCount = 0;

foreach ($bladeFiles as $filePath) {
    $inspectedViewsCount++;
    $relPath = str_replace('\\', '/', substr($filePath, strlen($viewsDir) + 1));
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);

    // 1. Audit Responsive Tables: Detect <table> without wrapping overflow-x-auto
    if (preg_match_all('/<table\b/i', $content, $matches)) {
        $totalTablesCount += count($matches[0]);
        // Check if the file contains overflow-x-auto or table-responsive wrapper
        if (!preg_match('/overflow-x-auto|overflow-x-scroll|table-responsive|table-wrapper/i', $content)) {
            // Find line numbers of table
            foreach ($lines as $lineNum => $line) {
                if (stripos($line, '<table') !== false) {
                    $findings['table_overflow_missing'][] = [
                        'file' => $relPath,
                        'line' => $lineNum + 1,
                        'snippet' => trim(substr($line, 0, 80)),
                    ];
                }
            }
        }
    }

    // 2. Audit Alpine.js inline attribute leaks (e.g. x-data="{ ... > ... }")
    foreach ($lines as $lineNum => $line) {
        // Look for x-data attributes containing unescaped > or unbalanced braces
        if (preg_match('/x-data="\{[^"]*>[^"]*\}"/i', $line, $m)) {
            $findings['alpine_bracket_leaks'][] = [
                'file' => $relPath,
                'line' => $lineNum + 1,
                'snippet' => trim($m[0]),
            ];
        }
    }

    // 3. Audit Hardcoded localhost / 127.0.0.1 URLs
    foreach ($lines as $lineNum => $line) {
        if (preg_match('/https?:\/\/(localhost|127\.0\.0\.1|187\.127\.)[:0-9]*/i', $line, $m)) {
            $findings['hardcoded_urls'][] = [
                'file' => $relPath,
                'line' => $lineNum + 1,
                'snippet' => trim($m[0]),
            ];
        }
    }

    // 4. Audit WCAG 2.1 Low Contrast Utility Combinations (e.g. text-slate-300 or text-gray-300 on white/slate-50)
    foreach ($lines as $lineNum => $line) {
        if (preg_match('/class="[^"]*\b(text-slate-300|text-gray-300|text-zinc-300)\b[^"]*"/i', $line, $m)) {
            // Check if used for text rather than icons or borders
            if (!preg_match('/border-|stroke-|fill-|icon/i', $m[0])) {
                $findings['low_contrast_classes'][] = [
                    'file' => $relPath,
                    'line' => $lineNum + 1,
                    'snippet' => trim(substr($line, 0, 80)),
                ];
            }
        }
    }

    // 5. Audit Unlabeled Inputs: form inputs lacking id, name, or aria-label
    if (stripos($content, '<form') !== false) {
        $totalFormsCount++;
        foreach ($lines as $lineNum => $line) {
            if (preg_match('/<input\b[^>]*type="(text|email|password|number|tel|date)"[^>]*>/i', $line, $m)) {
                $tag = $m[0];
                if (!preg_match('/\b(id|aria-label|aria-labelledby|placeholder)=/i', $tag)) {
                    $findings['unlabeled_inputs'][] = [
                        'file' => $relPath,
                        'line' => $lineNum + 1,
                        'snippet' => trim($tag),
                    ];
                }
            }
        }
    }

    // 6. Audit Blade Directive balance
    $directives = [
        'if' => ['open' => '/@if\b/', 'close' => '/@endif\b/'],
        'foreach' => ['open' => '/@foreach\b/', 'close' => '/@endforeach\b/'],
        'can' => ['open' => '/@can\b/', 'close' => '/@endcan\b/'],
        'section' => ['open' => '/@section\b/', 'close' => '/@(endsection|stop|show)\b/'],
    ];
    foreach ($directives as $dirName => $patterns) {
        $openCount = preg_match_all($patterns['open'], $content);
        $closeCount = preg_match_all($patterns['close'], $content);
        if ($openCount !== $closeCount) {
            $findings['unclosed_directives'][] = [
                'file' => $relPath,
                'directive' => $dirName,
                'open' => $openCount,
                'close' => $closeCount,
            ];
        }
    }
}

echo "AUDIT SUMMARY RESULTS:\n";
echo "--------------------------------------------------------------------\n";
echo "- Total Views Inspected            : $inspectedViewsCount\n";
echo "- Total Data Tables Inspected      : $totalTablesCount\n";
echo "- Tables Missing Overflow Wrapper  : " . count($findings['table_overflow_missing']) . "\n";
echo "- Total Forms Inspected            : $totalFormsCount\n";
echo "- Unlabeled Inputs Detected        : " . count($findings['unlabeled_inputs']) . "\n";
echo "- Potential Inline Alpine Leaks    : " . count($findings['alpine_bracket_leaks']) . "\n";
echo "- Hardcoded Localhost URLs         : " . count($findings['hardcoded_urls']) . "\n";
echo "- Low-Contrast Text Classes        : " . count($findings['low_contrast_classes']) . "\n";
echo "- Blade Directive Imbalances       : " . count($findings['unclosed_directives']) . "\n\n";

// Save results
file_put_contents("$qaDir/ui_ux_results.json", json_encode([
    'audit_metadata' => [
        'stage' => 'Stage 7',
        'title' => 'UI, UX, Responsive Design & Accessibility Audit',
        'executed_at' => date('Y-m-d H:i:s'),
        'total_views' => $inspectedViewsCount,
        'total_tables' => $totalTablesCount,
        'total_forms' => $totalFormsCount,
    ],
    'findings' => $findings,
], JSON_PRETTY_PRINT));

echo "Full UI/UX audit dataset saved to: $qaDir/ui_ux_results.json\n";

