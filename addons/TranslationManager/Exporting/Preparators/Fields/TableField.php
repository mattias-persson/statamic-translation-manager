<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

class TableField
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
