<?php

namespace Statamic\Addons\TranslationManager\Importing\Preparators\Fields;

class ArrayField extends Field
{
    /**
     * Parse the translation.
     *
     * @return array
     */
    public function map($translation)
    {
        $fieldName = explode('.', $translation['field_name']);
        $units = $this->getUnitsInTheSameField($fieldName[0]);
        $translations = [];

        // Loop through all the original values in the array/list.
        foreach ($this->item['original']->get($fieldName[0]) as $index => $value) {

            // Loop through all translated values to find matches.
            foreach ($units as $unit) {
                if ($unit['source'] === $value) {
                    $translations[$index] = $unit['target'];
                }
            }
        }

        return $translations;
    }

    protected function getUnitsInTheSameField($fieldName)
    {
        return collect($this->item['translations'])->filter(function ($unit) use ($fieldName) {
            return starts_with($unit['field_name'], $fieldName.'.');
        })->toArray();
    }
}
