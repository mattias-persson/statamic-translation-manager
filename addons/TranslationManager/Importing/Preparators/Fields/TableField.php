<?php

namespace Statamic\Addons\TranslationManager\Importing\Preparators\Fields;

class TableField extends Field
{
    /**
     * Parse the translation.
     *
     * @return array
     */
    public function map($translation)
    {
        $fieldName = explode('.', $translation['field_name']);

        $translations = [];

        // Loop through all the rows in the table.
        foreach ($this->item['original']->get($fieldName[0]) as $rowIndex => $value) {
            $units = $this->getUnitsInTheSameRow($fieldName[0], $rowIndex);
            $translations[$rowIndex]['cells'] = [];

            // Loop through each original table cell value.
            foreach ($value['cells'] as $colIndex => $originalValue) {

                // Loop through all translated values to find matches.
                foreach ($units as $unit) {
                    if ($unit['source'] === $originalValue) {
                        $translations[$rowIndex]['cells'][$colIndex] = $unit['target'];
                    }
                }
            }
        }

        return $translations;
    }

    /**
     * Returns units belonging to the same row in the same field.
     *
     * @param string $fieldName
     * @param int $rowIndex
     * @return array
     */
    protected function getUnitsInTheSameRow($fieldName, $rowIndex)
    {
        return collect($this->item['translations'])->filter(function ($unit) use ($fieldName, $rowIndex) {
            return starts_with($unit['field_name'], $fieldName.'.'.$rowIndex);
        })->toArray();
    }
}
