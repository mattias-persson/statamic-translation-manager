<?php

namespace Statamic\Addons\TranslationManager\Importing\Parsers;

class XliffParser
{
    /**
     * The incoming XML to parse.
     *
     * @var object
     */
    protected $xml;

    /**
     * Loads and reads the file contents.
     *
     * @param object $data
     */
    public function __construct($data)
    {
        $this->xml = simplexml_load_string($data);
    }

    /**
     * Parses the xml from the xliff file into an common-structured array.
     *
     * @return Illuminate\Support\Collection
     */
    public function parse()
    {
        $items = [];

        foreach ($this->xml->file as $item) {
            $items[] = [
                'meta_data' => $this->getMetaData($item),
                'translations' => $this->getTranslations($item),
            ];
        }

        return collect($items);
    }

    /**
     * Returns the info about the item, in other words the page/entry etc.
     *
     * @param object $item
     * @return array
     */
    public function getMetaData($item)
    {
        return [
            'source_language' => (string) $item->attributes()->{'source-language'},
            'target_language' => (string) $item->attributes()->{'target-language'},
            'type' => explode(':', (string) $item->attributes()->{'original'})[0],
            'id' => explode(':', (string) $item->attributes()->{'original'})[1],
        ];
    }

    /**
     * Parses the translations from the xliff file.
     *
     * @param object $item
     * @return array
     */
    public function getTranslations($item)
    {
        $translations = [];

        foreach (array_values((array) $item->body)[0] as $unit) {
            $metaData = explode(':', (string) $unit->attributes()->id);

            $translations[] = [
                'field_name' => $metaData[0],
                'field_type' => $metaData[1],
                'source' => (string) $unit->source,
                'target' => (string) $unit->target,
            ];
        }

        return $translations;
    }
}
