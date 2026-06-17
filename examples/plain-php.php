<?php

declare(strict_types=1);

use AbaConnect\Abacus\Export\AbacusXmlExportConfig;
use AbaConnect\Abacus\Export\AbacusXmlExporter;
use AbaConnect\Abacus\Mapper\ArrayTimesheetEntryMapper;

require __DIR__ . '/../vendor/autoload.php';

$rawRows = [
    [
        'ligne1' => 'EMP001',
        'ligne2' => '2026-06-17',
        'ligne3' => '06',
        'ligne4' => '100',
        'ligne5' => '8.5',
        'ligne6' => '1',
        'ligne7' => '200',
        'ligne8' => 'Texte paie',
    ],
    [
        'ligne1' => 'EMP002',
        'ligne2' => '2026-06-17',
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
    $entries[] = $mapper->map((object) $row);
}

$config = new AbacusXmlExportConfig(
    mandant: '648702',
    application: 'LOHN',
    id: 'FlatPreEntry',
    mapId: 'AbaDefault',
    version: '2020.00',
);

$exporter = new AbacusXmlExporter();
$xml = $exporter->exportToString($entries, $config);

file_put_contents(__DIR__ . '/../exportedEntries.xml', $xml);

echo $xml;

