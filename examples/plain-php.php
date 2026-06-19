<?php

declare(strict_types=1);

use AbaConnect\Abacus\Export\AbacusXmlExportConfig;
use AbaConnect\Abacus\Export\AbacusXmlExporter;
use AbaConnect\Abacus\Mapper\ArrayTimesheetEntryMapper;

require __DIR__ . '/../vendor/autoload.php';

$rawRows = [
    [
        'ligne1' => 1,
        'ligne2' => new DateTime('2026-01-01'),
        'ligne3' => '06',
        'ligne4' => '100',
        'ligne5' => '8.5',
        'ligne6' => '1',
        'ligne7' => '200',
        'ligne8' => 'Texte paie',
    ],
    [
        'ligne1' => 2,
        'ligne2' => new DateTime('2026-01-02'),
        'ligne3' => '06',
        'ligne4' => '100',
        'ligne5' => '8.5',
        'ligne6' => '1',
        'ligne7' => '200',
        'ligne8' => null,
    ],
];

$mapper = new ArrayTimesheetEntryMapper();
$entries = [];

foreach ($rawRows as $row) {
    $entries[] = $mapper->map((object)$row);
}

$config = new AbacusXmlExportConfig(
    mandant: '123456',
    prettyPrint: true,
    validateEntries: true
);

$exporter = new AbacusXmlExporter();
try {
    $xml = $exporter->exportToString($entries, $config);
} catch (DOMException $e) {
    throw new RuntimeException('Erreur lors de l\'export XML: ' . $e->getMessage(), previous: $e);
}

file_put_contents(__DIR__ . '/../exportedEntries.xml', $xml);

echo $xml;

