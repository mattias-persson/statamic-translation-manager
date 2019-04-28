<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

class ReplicatorField
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
        foreach ($data['localized_value'] as $rowIndex => $set) {
            $setName = $set['type'];
            unset($set['type']);

            foreach ($set as $field => $value) {
                $key = $data['field_name'].'.'.$setName.'.'.$field;

                // Text fields within the replicator.
                if (is_string($value)) {
                    $this->fields[$key] = [
                        'type' => $data['field_type'],
                        'name' => $key.':'.$data['field_type'],
                        'original' => $data['original_value'][$rowIndex][$field],
                        'localized' => $value,
                    ];
                } elseif (is_array($value)) {
                    $itemIndex = 0;
                    foreach ($value as $string) {
                        $key = $data['field_name'].'.'.$setName.'.'.$field.'.'.$itemIndex;
                        $this->fields[$key] = [
                            'type' => $data['field_type'],
                            'name' => $key.':'.$data['field_type'],
                            'original' => collect($data['original_value'][$rowIndex][$field])->values()[$itemIndex],
                            'localized' => $string,
                        ];

                        $itemIndex++;
                    }
                }
            }
        }

        return $this->fields;
    }
}
