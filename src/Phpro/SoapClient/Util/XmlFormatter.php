<?php namespace Phpro\SoapClient\Util;

class XmlFormatter
{

    public static function format(string $xml): string
    {
        if (!$xml) {
            return '';
        }

        $doc = new \DOMDocument('1.0');
        $doc->formatOutput = true;

        if (strpos(strtolower($xml), '<html') !== false) {
            if ($doc->loadHTML($xml)) {
                return $doc->saveHTML();
            }
        } elseif ($doc->loadXML($xml)) {
            return $doc->saveXML();
        }

        return $xml;
    }
}
