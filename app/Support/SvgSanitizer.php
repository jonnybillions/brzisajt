<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

/**
 * Čisti SVG od skripti, rukovalaca događajima i spoljnih linkova —
 * za logotipe koji se otpremaju u admin panelu i prikazuju na sajtu.
 */
class SvgSanitizer
{
    private const ALLOWED_TAGS = [
        'svg', 'g', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon', 'text', 'tspan',
        'defs', 'lineargradient', 'radialgradient', 'stop', 'clippath', 'mask', 'use', 'symbol', 'title', 'desc',
    ];

    public static function clean(?string $svg): ?string
    {
        if (! filled($svg) || ! str_contains($svg, '<svg')) {
            return null;
        }

        $doc = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $doc->loadXML(trim($svg), LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded || ! $doc->documentElement || strtolower($doc->documentElement->localName) !== 'svg') {
            return null;
        }

        self::walk($doc->documentElement);

        return $doc->saveXML($doc->documentElement) ?: null;
    }

    private static function walk(DOMElement $el): void
    {
        foreach (iterator_to_array($el->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                if (! in_array(strtolower($child->localName), self::ALLOWED_TAGS, true)) {
                    $el->removeChild($child);

                    continue;
                }
                self::walk($child);
            } elseif ($child->nodeType === XML_PI_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                $el->removeChild($child);
            }
        }

        foreach (iterator_to_array($el->attributes) as $attr) {
            $name = strtolower($attr->nodeName);
            $value = strtolower(preg_replace('/\s+/', '', $attr->nodeValue));

            $isEvent = str_starts_with($name, 'on');
            $isLink = in_array($name, ['href', 'xlink:href'], true) && ! str_starts_with($value, '#');
            $isScriptValue = str_contains($value, 'javascript:') || str_contains($value, 'url(http') || str_contains($value, 'expression(');

            if ($isEvent || $isLink || $isScriptValue) {
                $el->removeAttribute($attr->nodeName);
            }
        }
    }
}
