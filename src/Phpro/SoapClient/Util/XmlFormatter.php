<?php namespace Phpro\SoapClient\Util;

class XmlFormatter
{

    public static function format(string $xml): string
    {
        if (!$xml) {
            return '';
        }

        $type = stripos($xml, '<html') !== false ? 'HTML' : 'XML';

        $doc = new \DOMDocument('1.0');
        $doc->formatOutput = true;
        if ($doc->{"load$type"}($xml)) {
            return $doc->{"save$type"}();
        }

        return $xml;
    }
}
