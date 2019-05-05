<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

class ArrayField extends Field
{
    /**
     * Parse and add the current field to the list of fields.
     *
     * @param array $data
     * @return array
     */
    public function map($data)
    {
        foreach ($data['original_value'] as $index => $string) {
            $this->fields[$data['field_name'].'.'.$index] = [
                'type' => $data['field_type'],
                'name' => $data['field_name'].'.'.$index.':'.$data['field_type'],
                'original' => $string,
                'localized' => $data['localized_value'][$index] ?? '',
            ];
        }

        return $this->fields;
    }
}
