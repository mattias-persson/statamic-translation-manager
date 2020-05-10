<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

use Illuminate\Support\Str;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ReplicatorField extends Field
{
    /**
     * Parse and add the current field to the list of fields.
     *
     * @param array $data
     * @return array
     */
    public function map($data)
    {
        $fields = $this->flatten($data['original_value'], $data['field_name']);

        if (empty($fields)) {
            return [];
        }

        $localizedFields = null;

        if (! empty($data['localized_value'])) {
            $localizedFields = $this->flatten($data['localized_value'], $data['field_name']);
        }

        foreach ($fields as $path => $value) {
            $localized = $localizedFields[$path] ?? '';

            if (Str::endsWith($path, '.type')) {
                $localized = $value;
            }

            $this->fields[$path] = [
                'type' => $data['field_type'],
                'name' => $path.':'.$data['field_type'],
                'original' => $value,
                'localized' => $localized,
            ];
        }

        return $this->fields;
    }

    /**
     * Flattens a nested array into a single level array with dot notation keys.
     *
     * @param array $array
     * @param string|null $prefix
     * @return array
     */
    protected function flatten($array, $prefix = null)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        $result = [];

        if ($prefix) {
            $prefix .= '.';
        }

        foreach ($iterator as $value) {
            $keys = [];

            foreach (range(0, $iterator->getDepth()) as $depth) {
                $keys[] = $iterator->getSubIterator($depth)->key();
            }

            $result[ $prefix.join('.', $keys) ] = $value;
        }

        return $result;
    }
}
