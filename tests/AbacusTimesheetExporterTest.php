<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Tests;

use PHPUnit\Framework\TestCase;
use Sp4tz\AbacusXmlExporter\Config\ExportConfig;
use Sp4tz\AbacusXmlExporter\Config\MappingConfig;
use Sp4tz\AbacusXmlExporter\Exporter\AbacusTimesheetExporter;
use Sp4tz\AbacusXmlExporter\Mapper\ConfigurableAbacusMapper;
use Sp4tz\AbacusXmlExporter\Model\TimesheetEntry;
use Sp4tz\AbacusXmlExporter\Validator\TimesheetValidator;
use Sp4tz\AbacusXmlExporter\Xml\AbacusXmlBuilder;

final class AbacusTimesheetExporterTest extends TestCase
{
    public function testItExportsValidTimesheetEntry(): void
    {
        $config = new ExportConfig(
            companyId: 'ECAP',
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
            new TimesheetValidator($config),
            new ConfigurableAbacusMapper($mapping, $config),
            new AbacusXmlBuilder(),
            $config,
        );

        $result = $exporter->export([
            new TimesheetEntry(
                employeeIdentifier: '12345',
                date: new \DateTimeImmutable('2026-05-05'),
                duration: 7.5,
                activityCode: 'EXERCICE',
                costCenter: '1000',
                projectCode: null,
                comment: 'Exercice pompier',
            ),
        ]);

        self::assertTrue($result->isSuccess());
        self::assertSame(1, $result->getExportedRows());
        self::assertStringContainsString('<AbaConnectContainer companyId="ECAP">', (string) $result->getXml());
        self::assertStringContainsString('<PersonNumber>12345</PersonNumber>', (string) $result->getXml());
        self::assertStringContainsString('<Hours>7.5</Hours>', (string) $result->getXml());
    }

    public function testStrictModeFailsOnInvalidEntry(): void
    {
        $config = new ExportConfig(strict: true);
        $exporter = new AbacusTimesheetExporter(
            new TimesheetValidator($config),
            new ConfigurableAbacusMapper(MappingConfig::fromArray([]), $config),
            new AbacusXmlBuilder(),
            $config,
        );

        $result = $exporter->export([
            new TimesheetEntry(
                employeeIdentifier: '',
                date: new \DateTimeImmutable('2026-05-05'),
                duration: 0,
            ),
        ]);

        self::assertFalse($result->isSuccess());
        self::assertGreaterThanOrEqual(2, count($result->getValidationResult()->getErrors()));
    }
}
