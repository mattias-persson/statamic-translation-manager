<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

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
        foreach ($data['original_value'] as $rowIndex => $set) {
            $setName = $set['type'];
            unset($set['type']);

            foreach ($set as $field => $value) {
                $key = $data['field_name'].'.'.$setName.'.'.$field;

                // Text fields within the replicator.
                if (is_string($value)) {
                    $this->fields[$key] = [
                        'type' => $data['field_type'],
                        'name' => $key.':'.$data['field_type'],
                        'original' => $value,
                        'localized' => $data['localized_value'][$rowIndex][$field] ?? '',
                    ];
                } elseif (is_array($value)) {
                    $itemIndex = 0;
                    foreach ($value as $string) {
                        $key = $data['field_name'].'.'.$setName.'.'.$field.'.'.$itemIndex;
                        try {
                            $localized = collect($data['localized_value'][$rowIndex][$field])->values()[$itemIndex];
                        } catch (\Exception $e) {
                            $localized = '';
                        }

                        $this->fields[$key] = [
                            'type' => $data['field_type'],
                            'name' => $key.':'.$data['field_type'],
                            'original' => $string,
                            'localized' => $localized,
                        ];

                        $itemIndex++;
                    }
                }
            }
        }

        return $this->fields;
    }
}
