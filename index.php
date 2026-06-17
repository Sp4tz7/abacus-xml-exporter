<?php

declare(strict_types=1);

use AbaConnect\Abacus\Export\AbacusXmlExportConfig;
use AbaConnect\Abacus\Export\AbacusXmlExporter;
use AbaConnect\Abacus\Mapper\ArrayTimesheetEntryMapper;
use AbaConnect\Abacus\Mapper\TimesheetEntryMapperInterface;
use AbaConnect\Abacus\Exception\AbacusXmlExportException;

require __DIR__ . '/vendor/autoload.php';

/*
 * ================================================================
 * Guide rapide - Utilisation de la librairie Abacus
 * ================================================================
 *
 * 1) Initialiser Composer (une seule fois)
 *    composer init -n --name="abaconnect/abacus-xml-exporter" --require="php:^8.2" --autoload='{"psr-4":{"AbaConnect\\Abacus\\":"src/Abacus/"}}'
 *
 * 2) Régénérer l'autoload a chaque changement de namespace/fichiers
 *    composer dump-autoload
 *
 * 3) Lancer ce script d'exemple
 *    php index.php
 *
 * Ce script montre le flux complet:
 * - donnees source (tableau brut)
 * - mapping vers des DTO TimesheetEntry
 * - export XML en string
 * - export XML dans un fichier local
 */

/**
 * Donnees d'entree "brutes".
 *
 * Contrat attendu par le mapper ci-dessous:
 * - ligne1: employeeNumber (string)
 * - ligne2: periodDate au format Y-m-d
 * - ligne3: periodNumber (string)
 * - ligne4: payrollType (int)
 * - ligne5: amount (float)
 * - ligne6: factor (int)
 * - ligne7: costCentre1 (int)
 * - ligne8: textPayrollType (string|null)
 */
$entries = [
    'entry 1' => [
        'ligne1' => 'EMP001',
        'ligne2' => '2026-06-17',
        'ligne3' => '06',
        'ligne4' => '100',
        'ligne5' => '8.5',
        'ligne6' => '1',
        'ligne7' => '200',
        'ligne8' => 'Texte paie',
    ],
    'entry 2' => [
        'ligne1' => 'EMP002',
        'ligne2' => '2026-06-17',
        'ligne3' => '06',
        'ligne4' => '100',
        'ligne5' => '8.5',
        'ligne6' => '1',
        'ligne7' => '200',
        'ligne8' => 'Texte paie',
    ],
    'entry 3' => [
        'ligne1' => 'EMP003',
        'ligne2' => '2026-06-17',
        'ligne3' => '06',
        'ligne4' => '100',
        'ligne5' => '8.5',
        'ligne6' => '1',
        'ligne7' => '200',
        'ligne8' => 'Texte paie',
    ],
    'entry 4' => [
        'ligne1' => 'EMP004',
        'ligne2' => '2026-06-17',
        'ligne3' => '06',
        'ligne4' => '100',
        'ligne5' => '8.5',
        'ligne6' => '1',
        'ligne7' => '200',
        'ligne8' => null,
    ]
];

/** @var \AbaConnect\Abacus\Dto\TimesheetEntry[] $formatedEntries */
$formatedEntries = [];

// Exemple concret d'usage du mapper/interface hors framework.
$sourceEntries = array_map(static fn(array $entry): object => (object) $entry, $entries);

/** @var TimesheetEntryMapperInterface $mapper */
$mapper = new ArrayTimesheetEntryMapper();

foreach ($sourceEntries as $sourceEntry) {
    $formatedEntries[] = $mapper->map($sourceEntry);
}

// Instancie l'exporter et la configuration.
$exporter = new AbacusXmlExporter();
 $config = new AbacusXmlExportConfig(); // version par defaut: 2020.00

// 1) Export en memoire (utile pour logs, API, debug, tests).
try {
    $xml = $exporter->exportToString($formatedEntries, $config);

    // Affiche le XML généré.
    var_dump($xml);
} catch ( AbacusXmlExportException $e) {
    var_dump("Erreur d'export XML: " . $e->getMessage());
}

// 2) Export vers un fichier XML.
$exporter->exportToFile($formatedEntries, $config, __DIR__ . '/exportedEntries.xml');


