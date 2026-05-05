<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Xml;

final class XmlFormatter
{
    public function format(string $xml): string
    {
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML($xml);

        return $document->saveXML() ?: $xml;
    }
}
