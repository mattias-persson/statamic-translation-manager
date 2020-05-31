<?php

namespace Statamic\Addons\TranslationManager\Importing;

use Statamic\API\Entry;
use Statamic\API\GlobalSet;
use Statamic\API\Page;
use Statamic\API\Term;

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
                // If the field hasn't been translated and the original language
                // has a value, apply the original one to prevent blank fields.
                if (empty($translation['target']) && ! empty($translation['source'])) {
                    $translation['target'] = $translation['source'];
                }

                $translations[$translation['field_name']] = $translation['target'];
            }

            // Convert the dot notations to a multilevel array.
            $result = [];
            foreach ($translations as $key => $value) {
                $this->toArray($result, $key, $value);
            }

            foreach ($result as $field => $value) {
                $item['original']
                    ->in($item['meta_data']['target_language'])
                    ->set($field, $value);
            }

            $item['original']->save();
        }
    }

    /**
     * Converts the dot notation keys to nested arrays.
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array
     */
    protected function toArray(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
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
