<?php

namespace Statamic\Addons\TranslationManager\Exporting\Exporters;

use Illuminate\Support\Collection;
use Statamic\Addons\TranslationManager\Helpers\ExportFilename;
use Statamic\Addons\TranslationManager\Exporting\Exporters\Support\XliffDocument;

class Xliff
{
    public function create(Collection $data)
    {
        $xliff = new XliffDocument();
        $fileinfo = new ExportFilename($data);

        foreach ($data as $locale => $items) {
            $query = '';

            foreach ($items as $item) {
                $xliff
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

                $xliff->file()->body(true);

                if (empty($item['fields'])) {
                    continue;
                }

                foreach ($item['fields'] as $field) {
                    // Apply the original value.
                    $xliff->file()->body()->unit(true)->setAttribute('id', $field['name'])->source(true)->setTextContent($field['original']);

                    // Apply the translated value.
                    $xliff->file()->body()->unit()->target(true)->setTextContent($field['localized']);
                }
            }

            $dom = $xliff->toDOM();

            #$filename = (count([1]) > 1) ? $locale : $this->getFilename();
            #$filepath = dirname(__FILE__).'/../exports/'.$filename.'.xlf';
            file_put_contents($fileinfo->filepath, $dom->saveXML());
        }

        return $fileinfo->filepath;

        // foreach ($data as $item) {
            // Exclude some pages
            // if (in_array($file['id'], $this->config['exclude_page_ids'])) {
            //     continue;
            // }
            // $url   = $this->config['page_url'] ? $this->config['page_url'] : env('APP_URL');
            // $query = $this->config['page_query_string'] ? $this->config['page_query_string'] : '';

        //     $xliff->file(true)->setAttributes([
        //         'source-language' => $item['source-language'],
        //         'target-language' => $item['target-language'],
        //         'datatype' => 'plaintext',
        //         'original' => $item['type'] . ':' . $item['id'],
        //     ])->header(true)
        //         ->skl(true)
        //         ->{'external-file'}(true);
        //     //->setAttribute('href', $url . $item['uri'] . $query);

        //     $xliff->file()->body(true);

        //     foreach ($item['units'] as $unit) {
        //         // Apply the source.
        //         $xliff->file()
        //             ->body()
        //             ->unit(true)
        //             ->setAttribute('id', $unit['name'])
        //             ->source(true)
        //             ->setTextContent($unit['source']);

        //         // Apply the target.
        //         $xliff->file()->body()->unit()->target(true)->setTextContent($unit['target']);
        //     }
        // }

        // $dom = $xliff->toDOM();

        // dd($dom);

        // $filename = (count($this->locales) > 1) ? $locale : $this->getFilename();
        // $filepath = $this->exportPath . $filename . '.xlf';
        // file_put_contents($filepath, $dom->saveXML());

        // return $filepath;
    }

    /**
     * Returns the filename for the file.
     *
     * @return string
     */
    protected function getFilename()
    {
        $name = 'translations-';
        $name .= (count($this->locales) > 1) ? 'all-languages' : $this->locales[0];
        $name .= '-' . date('Y-m-d-His');

        return $name;
    }
}
