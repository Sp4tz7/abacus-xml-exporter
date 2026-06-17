# Abacus XML Exporter (PHP)

Librairie PHP pour convertir des entrees de feuille de temps en XML Abacus LOHN `FlatPreEntry`.

## Prerequis

- PHP 8.2+
- Composer

## Installation

Dans un projet PHP classique ou Symfony:

```powershell
composer require abaconnect/abacus-xml-exporter
```

En developpement local:

```powershell
composer dump-autoload
```

## Compatibilite Abacus

Ce package cible **Abacus LOHN FlatPreEntry 2020.00**.

Documentation officielle:

https://downloads.abacus.ch/fileadmin/ablage/abaconnect/htmlfiles/lohn/LOHN__FlatPreEntry_2020.00_AbaDefault_FR.html

## Configuration obligatoire

Avant le premier export, configure les donnees de ta societe dans `src/Abacus/Export/AbacusXmlExportConfig.php`.

Champs principaux a renseigner:

- `mandant`
- `application`
- `id`
- `mapId`
- `version`

Options utiles:

- `validateEntries` : active ou desactive la validation avant export
- `prettyPrint` : XML lisible (dev) ou compact (prod)

Exemple:

```php
$config = new AbacusXmlExportConfig(
    mandant: '648702',
    application: 'LOHN',
    id: 'FlatPreEntry',
    mapId: 'AbaDefault',
    version: '2020.00',
);
```

## Exemples d'utilisation

### 1) Usage PHP simple hors Symfony

Lance directement l'exemple autonome:

```powershell
php examples/plain-php.php
```

Cet exemple montre le flux complet:

- tableaux bruts `ligne1..ligne8`
- `ArrayTimesheetEntryMapper`
- export XML en string
- export XML dans `exportedEntries.xml`

### 2) Usage avec un DTO metier / Symfony

La librairie expose un contrat de mapping via `AbaConnect\Abacus\Mapper\TimesheetEntryMapperInterface`.

Exemple:

```php
use AbaConnect\Abacus\Mapper\TimesheetEntryMapperInterface;
use AbaConnect\Abacus\Mapper\ArrayTimesheetEntryMapper;

/** @var TimesheetEntryMapperInterface $mapper */
$mapper = new ArrayTimesheetEntryMapper();
```

## Classes principales

- `src/Abacus/Dto/TimesheetEntry.php`
- `src/Abacus/Export/AbacusXmlExporter.php`
- `src/Abacus/Export/AbacusXmlExportConfig.php`
- `src/Abacus/Exception/AbacusXmlExportException.php`
- `src/Abacus/Mapper/TimesheetEntryMapperInterface.php`
- `src/Abacus/Mapper/ArrayTimesheetEntryMapper.php`
- `src/Abacus/Mapper/ExampleTimesheetEntryMapper.php`

Namespaces utilises:

- `AbaConnect\Abacus\Dto`
- `AbaConnect\Abacus\Export`
- `AbaConnect\Abacus\Exception`
- `AbaConnect\Abacus\Mapper`

## Exemple minimal

```php
<?php

declare(strict_types=1);

use AbaConnect\Abacus\Dto\TimesheetEntry;
use AbaConnect\Abacus\Export\AbacusXmlExportConfig;
use AbaConnect\Abacus\Export\AbacusXmlExporter;

require __DIR__ . '/vendor/autoload.php';

$entries = [
    new TimesheetEntry(
        employeeNumber: 'EMP001',
        periodDate: new DateTimeImmutable('2026-06-17'),
        periodNumber: '06',
        payrollType: 100,
        amount: 8.5,
        factor: 1,
        costCentre1: 200,
        textPayrollType: 'Texte paie',
    ),
];

$exporter = new AbacusXmlExporter();
$config = new AbacusXmlExportConfig();

echo $exporter->exportToString($entries, $config);
```

## Gestion d'erreurs

Les erreurs d'export levent `AbaConnect\Abacus\Exception\AbacusXmlExportException`.


```php
try {
    $xml = $exporter->exportToString($entries, $config);
} catch (\AbaConnect\Abacus\Exception\AbacusXmlExportException $e) {
    // Log / message utilisateur
}
```

## Publié sur Packagist
 

