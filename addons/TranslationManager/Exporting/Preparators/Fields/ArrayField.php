<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

class ArrayField
{
    /**
     * The processed fields to which we want to add the data.
     *
     * @var array
     */
    protected $fields;

    /**
     * Apply the fields to the field mapper.
     *
     * @param array $fields
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Parse and add the current field to the list of fields.
     *
     * @param array $data
     * @return array
     */
    public function map($data)
    {
        foreach ($data['localized_value'] as $index => $string) {
            $this->fields[$data['field_name'].'.'.$index] = [
                'type' => $data['field_type'],
                'name' => $data['field_name'].'.'.$index.':'.$data['field_type'],
                'original' => $data['original_value'][$index],
                'localized' => $string,
            ];
        }

        return $this->fields;
    }
}
