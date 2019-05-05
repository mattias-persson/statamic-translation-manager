<?php

namespace Statamic\Addons\TranslationManager\Exporting\Exporters;

use Statamic\Addons\TranslationManager\Helpers\Config;
use Statamic\Addons\TranslationManager\Exporting\Exporters\Support\XliffDocument;

class Xliff
{
    protected $config;

    /**
     * The Xliff object used to assemble the result file.
     *
     * @var XliffDocument
     */
    protected $xliff;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Creates the Xliff file.
     *
     * @param string $locale
     * @param array $data
     * @return string
     */
    public function create($locale, $data)
    {
        $this->xliff = new XliffDocument();

        foreach ($data as $item) {
            if (!empty($this->config['page_url'])) {
                $href = $this->config['page_url'].'/'.$item['meta']['uri'];
            } else {
                $href = $item['meta']['url'];
            }

            if (!empty($this->config['page_query_string'])) {
                $href .= $this->config['page_query_string'];
            }

            $this->xliff
                ->file(true)
                ->setAttributes([
                    'source-language' => $item['meta']['source-language'],
                    'target-language' => $item['meta']['target-language'],
                    'datatype' => 'plaintext',
                    'original' => $item['meta']['type'] . ':' . $item['meta']['id'],
                ])
                ->header(true)
                ->skl(true)
                ->{'external-file'}(true)
                ->setAttribute('href', $href);

            $this->xliff->file()->body(true);

            if (empty($item['fields'])) {
                continue;
            }

            foreach ($item['fields'] as $field) {
                $this->addUnit($field);
            }
        }

        $filepath = Config::get('export_path').$locale.'.xlf';
        file_put_contents($filepath, $this->xliff->toDOM()->saveXML());

        return $filepath;
    }

    /**
     * Adds a translation unit (a field) to the file.
     * A unit consists of a target (the original value) and
     * a source (the translated value).
     *
     * @param array $field
     * @return void
     */
    protected function addUnit($field)
    {
        // Apply the original value.
        $this->xliff->file()->body()->unit(true)->setAttribute('id', $field['name'])->source(true)->setTextContent($field['original']);

        // Apply the translated value.
        $this->xliff->file()->body()->unit()->target(true)->setTextContent($field['localized']);
    }
}
