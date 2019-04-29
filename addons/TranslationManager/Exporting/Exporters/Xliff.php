<?php

namespace Statamic\Addons\TranslationManager\Exporting\Exporters;

use Statamic\Addons\TranslationManager\Exporting\Exporters\Support\XliffDocument;

class Xliff
{
    protected $xliff;

    public function create($locale, $data)
    {
        $this->xliff = new XliffDocument();

        foreach ($data as $item) {
            $query = '';

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
                ->setAttribute('href', $item['meta']['url'] . $query);

            $this->xliff->file()->body(true);

            if (empty($item['fields'])) {
                continue;
            }

            foreach ($item['fields'] as $field) {
                $this->addUnit($field);
            }
        }

        $filepath = dirname(__FILE__).'/../exports/'.$locale.'.xlf';
        file_put_contents($filepath, $this->xliff->toDOM()->saveXML());

        return $filepath;
    }

    protected function addUnit($field)
    {
        // Apply the original value.
        $this->xliff->file()->body()->unit(true)->setAttribute('id', $field['name'])->source(true)->setTextContent($field['original']);

        // Apply the translated value.
        $this->xliff->file()->body()->unit()->target(true)->setTextContent($field['localized']);
    }
}
