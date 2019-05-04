<?php

namespace Statamic\Addons\TranslationManager\Importing;

use Statamic\API\Page;
use Statamic\API\Term;
use Statamic\API\Entry;
use Statamic\API\GlobalSet;
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

                    // This includes:
                    // - Regular text fields
                    // - Bard
                    // - Textareas
                    // - Markdown
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
        switch (strtolower($item['meta_data']['type'])) {
            case 'page':
                return Page::find($item['meta_data']['id']);
                break;

            case 'entry':
                return Entry::find($item['meta_data']['id']);
                break;

            case 'term':
                return Term::find($item['meta_data']['id']);
                break;

            case 'globalset':
                return GlobalSet::find($item['meta_data']['id']);
                break;
        }
    }
}
