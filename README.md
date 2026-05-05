# sp4tz/abacus-xml-exporter

Generic PHP/Symfony XML exporter for Abacus timesheet imports.

The package is designed to stay framework-agnostic. Symfony support is only an optional integration layer.

## Basic usage

```php
use Sp4tz\AbacusXmlExporter\Config\ExportConfig;
use Sp4tz\AbacusXmlExporter\Config\MappingConfig;
use Sp4tz\AbacusXmlExporter\Exporter\AbacusTimesheetExporter;
use Sp4tz\AbacusXmlExporter\Mapper\ConfigurableAbacusMapper;
use Sp4tz\AbacusXmlExporter\Model\TimesheetEntry;
use Sp4tz\AbacusXmlExporter\Validator\TimesheetValidator;
use Sp4tz\AbacusXmlExporter\Xml\AbacusXmlBuilder;

$config = new ExportConfig(
    rootNode: 'AbaConnectContainer',
    recordNode: 'TimeSheet',
    strict: true,
);

$mapping = MappingConfig::fromArray([
    'employee_id' => 'PersonNumber',
    'date' => 'Date',
    'duration' => 'Hours',
    'activity_code' => 'Activity',
    'cost_center' => 'CostCenter',
    'project_code' => 'Project',
    'comment' => 'Text',
]);

$exporter = new AbacusTimesheetExporter(
    new TimesheetValidator(),
    new ConfigurableAbacusMapper($mapping, $config),
    new AbacusXmlBuilder(),
    $config,
);

$result = $exporter->export([
    new TimesheetEntry(
        employeeIdentifier: '12345',
        date: new DateTimeImmutable('2026-05-05'),
        duration: 7.5,
        activityCode: 'EXERCICE',
        costCenter: '1000',
        projectCode: null,
        comment: 'Exercice pompier'
    ),
]);

if (!$result->isSuccess()) {
    var_dump($result->getValidationResult()->getErrors());
}

file_put_contents('abacus.xml', $result->getXml());
```

## Symfony configuration example

```yaml
abacus_xml_exporter:
  company_id: 'ECAP'
  root_node: 'AbaConnectContainer'
  record_node: 'TimeSheet'
  strict: true
  encoding: 'UTF-8'
  date_format: 'Y-m-d'
  decimal_separator: '.'
  skip_null_values: true
  required_fields:
    - employee_id
    - date
    - duration
  mapping:
    employee_id: 'PersonNumber'
    date: 'Date'
    duration: 'Hours'
    activity_code: 'Activity'
    cost_center: 'CostCenter'
    project_code: 'Project'
    comment: 'Text'
```

## Philosophy

Your application entities should not be passed directly to the exporter. Create adapters implementing `TimesheetEntryInterface`.
