<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

class StringField extends Field
{
    /**
     * Parse and add the current field to the list of fields.
     *
     * @param array $data
     * @return array
     */
    public function map($data)
    {
        $this->fields[$data['field_name']] = [
            'type' => $data['field_type'],
            'name' => $data['field_name'].':'.$data['field_type'],
            'original' => $data['original_value'],
            'localized' => $data['localized_value'],
        ];

        return $this->fields;
    }
}
