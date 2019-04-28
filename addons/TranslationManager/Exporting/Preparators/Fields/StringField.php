<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

class StringField
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
        $this->fields[$data['field_name']] = [
            'type' => $data['field_type'],
            'name' => $data['field_name'].':'.$data['field_type'],
            'original' => $data['original_value'],
            'localized' => $data['localized_value'],
        ];

        return $this->fields;
    }
}
