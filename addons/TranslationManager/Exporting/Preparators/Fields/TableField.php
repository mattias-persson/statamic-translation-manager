<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

class TableField extends Field
{
    /**
     * Parse and add the current field to the list of fields.
     *
     * @param array $data
     * @return array
     */
    public function map($data)
    {
        foreach ($data['localized_value'] as $rowIndex => $cells) {
            foreach ($cells as $cell) {
                foreach ($cell as $cellIndex => $string) {
                    $key = $data['field_name'].'.'.$rowIndex.'.'.$cellIndex;

                    $this->fields[$key] = [
                        'type' => $data['field_type'],
                        'name' => $key.':'.$data['field_type'],
                        'original' => $data['original_value'][$rowIndex]['cells'][$cellIndex],
                        'localized' => $string,
                    ];
                }
            }
        }

        return $this->fields;
    }
}
