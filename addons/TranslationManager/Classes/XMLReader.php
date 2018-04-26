<?php

namespace Statamic\Addons\TranslationManager\Classes;

class XMLReader
{
    protected $xml;

    public function read($file)
    {
        $this->xml = simplexml_load_string(file_get_contents($file));

        return $this->readPages();
    }

    public function readPages()
    {
        $pages = [];

        foreach ($this->xml->file as $page) {
            $pages[] = new Importable($page);
        }

        return collect($pages);
    }

    public static function parseXmlAttributes($xmlAttributes)
    {
        $data = [];

        foreach ($xmlAttributes as $key => $attribute) {
            $data[$key] = (string) $attribute;
        }

        return $data;
    }

    public static function parseXmlUnits($xml)
    {
        $units = [];

        foreach ($xml->body->{'trans-unit'} as $unit) {
            $attributes = XMLReader::parseXmlAttributes($unit->attributes());

            $units[] = [
                'field'  => $attributes['id'],
                'source' => (string) $unit->source,
                'target' => (string) $unit->target,
            ];
        }

        return $units;
    }
}
