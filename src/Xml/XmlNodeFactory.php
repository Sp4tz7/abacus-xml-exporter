<?php

declare(strict_types=1);

namespace Sp4tz\AbacusXmlExporter\Xml;

final class XmlNodeFactory
{
    public function appendTextNode(\DOMDocument $document, \DOMElement $parent, string $name, string|int|float|bool|null $value): void
    {
        $node = $document->createElement($this->sanitizeNodeName($name));
        $node->appendChild($document->createTextNode($this->normalizeValue($value)));
        $parent->appendChild($node);
    }

    private function normalizeValue(string|int|float|bool|null $value): string
    {
        return match (true) {
            $value === null => '',
            is_bool($value) => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    private function sanitizeNodeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            return 'Field';
        }

        return preg_replace('/[^A-Za-z0-9_:\-\.]/', '_', $name) ?: 'Field';
    }
}
