<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Xml;

use Sp4tz\AbacusXmlExporter\Config\ExportConfig;
use Sp4tz\AbacusXmlExporter\Exception\AbacusXmlExporterException;

final readonly class AbacusXmlBuilder
{
    public function __construct(private XmlNodeFactory $nodeFactory = new XmlNodeFactory())
    {
    }

    /**
     * @param list<array<string, scalar|null>> $rows
     */
    public function build(array $rows, ExportConfig $config): string
    {
        $document = new \DOMDocument('1.0', $config->encoding);
        $document->formatOutput = true;

        $root = $document->createElement($config->rootNode);
        $document->appendChild($root);

        if ($config->companyId !== null) {
            $root->setAttribute('companyId', $config->companyId);
        }

        foreach ($rows as $row) {
            $recordNode = $document->createElement($config->recordNode);

            foreach ($row as $nodeName => $value) {
                if ($config->skipNullValues && $value === null) {
                    continue;
                }

                $this->nodeFactory->appendTextNode($document, $recordNode, (string) $nodeName, $value);
            }

            $root->appendChild($recordNode);
        }

        $xml = $document->saveXML();

        if ($xml === false) {
            throw new AbacusXmlExporterException('Unable to generate XML. Because apparently even XML can have moods.');
        }

        return $xml;
    }
}
