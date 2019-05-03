<?php

namespace Statamic\Addons\TranslationManager\Importing;

use Statamic\API\Page;

class Importer
{
    /**
     * Runs the import.
     *
     * @param Collection $data
     * @return void
     */
    public function import($data)
    {
        foreach ($data as $item) {
            if (!($original = $this->getOriginal($item))) {
                continue;
            }

            foreach ($item['translations'] as $translation) {
                $original
                    ->in($item['meta_data']['target_language'])
                    ->set($translation['field_name'], $translation['target']);

                $original->save();
            }
        }
    }

    /**
     * Retrieves the original item.
     *
     * @param array $item
     * @return mixed
     */
    protected function getOriginal($item)
    {
        if (strtolower($item['meta_data']['type']) === 'page') {
            return Page::find($item['meta_data']['id']);
        } else {
            return Entry::find($item['meta_data']['id']);
        }
    }
}
