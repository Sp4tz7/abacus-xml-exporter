<?php

namespace AbaConnect\Abacus\Export;

final readonly class AbacusXmlExportConfig
{
    public function __construct(
        public string $mandant = '648702',
        public string $sourceSystem = 'Symfony',
        public string $encoding = 'UTF-8',
        public bool $prettyPrint = true,
        public bool $validateEntries = true,
        public string $application = 'LOHN',
        public string $id = 'FlatPreEntry',
        public string $mapId = 'AbaDefault',
        public string $version = '2020.00',
    ) {
    }
}
