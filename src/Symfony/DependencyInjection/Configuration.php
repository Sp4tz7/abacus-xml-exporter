<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('abacus_xml_exporter');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('company_id')->defaultNull()->end()
                ->scalarNode('root_node')->defaultValue('AbaConnectContainer')->end()
                ->scalarNode('record_node')->defaultValue('TimeSheet')->end()
                ->booleanNode('strict')->defaultTrue()->end()
                ->scalarNode('encoding')->defaultValue('UTF-8')->end()
                ->scalarNode('date_format')->defaultValue('Y-m-d')->end()
                ->scalarNode('decimal_separator')->defaultValue('.')->end()
                ->booleanNode('skip_null_values')->defaultTrue()->end()
                ->arrayNode('required_fields')
                    ->scalarPrototype()->end()
                    ->defaultValue(['employee_id', 'date', 'duration'])
                ->end()
                ->arrayNode('defaults')
                    ->normalizeKeys(false)
                    ->scalarPrototype()->end()
                    ->defaultValue([])
                ->end()
                ->arrayNode('mapping')
                    ->normalizeKeys(false)
                    ->scalarPrototype()->end()
                    ->defaultValue([
                        'employee_id' => 'PersonNumber',
                        'date' => 'Date',
                        'duration' => 'Hours',
                        'activity_code' => 'Activity',
                        'cost_center' => 'CostCenter',
                        'project_code' => 'Project',
                        'comment' => 'Text',
                    ])
                ->end()
            ->end();

        return $treeBuilder;
    }
}
