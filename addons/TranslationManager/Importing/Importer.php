<?php

namespace Statamic\Addons\TranslationManager\Importing;

use Statamic\API\Page;
use Statamic\Addons\TranslationManager\Importing\Preparators\Fields\ArrayField;
use Statamic\Addons\TranslationManager\Importing\Preparators\Fields\TableField;

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
            if (!($item['original'] = $this->getOriginal($item))) {
                continue;
            }

            $translations = [];

            foreach ($item['translations'] as $translation) {
                switch ($translation['field_type']) {
                    case 'list':
                    case 'array':
                    case 'tags':
                        $field = explode('.', $translation['field_name']);
                        $translations[$field[0]] = (new ArrayField($item))->map($translation);
                        break;

                    case 'table':
                        $field = explode('.', $translation['field_name']);
                        $translations[$field[0]] = (new TableField($item))->map($translation);
                        break;

                    default:
                        $translations[$translation['field_name']] = $translation['target'];
                        break;
                }
            }


            foreach ($translations as $field => $value) {
                $item['original']
                    ->in($item['meta_data']['target_language'])
                    ->set($field, $value);
            }

            $item['original']->save();
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
