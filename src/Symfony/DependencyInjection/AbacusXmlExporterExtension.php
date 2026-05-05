<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Symfony\DependencyInjection;

use Sp4tz\AbacusXmlExporter\Config\ExportConfig;
use Sp4tz\AbacusXmlExporter\Config\MappingConfig;
use Sp4tz\AbacusXmlExporter\Contract\AbacusMapperInterface;
use Sp4tz\AbacusXmlExporter\Contract\XmlExporterInterface;
use Sp4tz\AbacusXmlExporter\Exporter\AbacusTimesheetExporter;
use Sp4tz\AbacusXmlExporter\Mapper\ConfigurableAbacusMapper;
use Sp4tz\AbacusXmlExporter\Validator\TimesheetValidator;
use Sp4tz\AbacusXmlExporter\Xml\AbacusXmlBuilder;
use Sp4tz\AbacusXmlExporter\Xml\XmlNodeFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class AbacusXmlExporterExtension extends Extension
{
    /**
     * @param array<int, array<string, mixed>> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container
            ->register(ExportConfig::class, ExportConfig::class)
            ->setArguments([
                $config['company_id'],
                $config['root_node'],
                $config['record_node'],
                $config['strict'],
                $config['encoding'],
                $config['date_format'],
                $config['decimal_separator'],
                $config['skip_null_values'],
                $config['required_fields'],
                $config['defaults'],
            ]);

        $container
            ->register(MappingConfig::class, MappingConfig::class)
            ->setArguments([$config['mapping']]);

        $container
            ->register(XmlNodeFactory::class, XmlNodeFactory::class);

        $container
            ->register(AbacusXmlBuilder::class, AbacusXmlBuilder::class)
            ->setArguments([new Reference(XmlNodeFactory::class)]);

        $container
            ->register(TimesheetValidator::class, TimesheetValidator::class)
            ->setArguments([new Reference(ExportConfig::class)]);

        $container
            ->register(ConfigurableAbacusMapper::class, ConfigurableAbacusMapper::class)
            ->setArguments([
                new Reference(MappingConfig::class),
                new Reference(ExportConfig::class),
            ]);

        $container->setAlias(AbacusMapperInterface::class, ConfigurableAbacusMapper::class)->setPublic(false);

        $container
            ->register(AbacusTimesheetExporter::class, AbacusTimesheetExporter::class)
            ->setArguments([
                new Reference(TimesheetValidator::class),
                new Reference(AbacusMapperInterface::class),
                new Reference(AbacusXmlBuilder::class),
                new Reference(ExportConfig::class),
            ]);

        $container->setAlias(XmlExporterInterface::class, AbacusTimesheetExporter::class)->setPublic(false);
    }
}
