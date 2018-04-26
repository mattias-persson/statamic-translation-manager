<?php

namespace Statamic\Addons\TranslationManager\Classes;

class Importer extends Translatable
{
    /**
     * Imports a file.
     *
     * @param  File $file
     * @return void
     */
    public function run($file)
    {
        $xmlReader = new XMLReader();

        $xmlReader->read($file)->each(function ($item) {
            $this->import($item);
        });
    }

    /**
     * Imports an item.
     *
     * @param  Statamic\Addons\TranslationManager\Classes\Importable $item
     * @return void
     */
    private function import($item)
    {
        // If the target language doesn't exist, copy the original data first.
        if (empty($item->target->data())) {
            $item->target->data($this->prepareData($item->original));
        }

        $data = $this->prepareData($item->target);
        $data = $this->applyTranslatedValues($item, $data);

        // Store the translated data on the item.
        if (!empty($data)) {
            $item->target->data($data)->save();
        }
    }

    /**
     * Applies the translated values to the fields.
     *
     * @param  Statamic\Addons\TranslationManager\Classes\Importable $item
     * @param  array $data
     * @return array
     */
    private function applyTranslatedValues($item, $data)
    {
        $originalData = $this->prepareData($item->original);

        // Apply the translated units on the translated data.
        foreach ($item->units as $unit) {
            $unit['field'] = str_replace('+', ' ', $unit['field']);

            $field = $this->getUnitFieldComponents($unit);

            // Only translate fields that exist on the original item.
            if (!isset($originalData[$field['name']])) {
                continue;
            }

            $unit['target'] = $this->prepareTargetValue($unit);

            switch ($field['type']) {
                case 'table':
                    $data = $this->applyTableValue($data, $unit);
                    break;

                case 'array':
                case 'list':
                case 'tags':
                    $data = $this->applyArrayValue($data, $unit);
                    break;

                // Strings.
                default:
                    $data[$field['name']] = $unit['target'];
                    break;
            }
        }

        return $data;
    }

    /**
     * Prepares the string value which will be applied.
     *
     * @param  array $unit
     * @return string
     */
    private function prepareTargetValue($unit)
    {
        $target = trim($unit['target']);

        // If no target is specified, apply the untranslated source value.
        if (!$target) {
            return trim($unit['source']);
        }

        return $target;
    }

    /**
     * Applies a table value and removes the old string value from the data.
     *
     * @param  array $data
     * @param  array $unit
     * @return array
     */
    private function applyTableValue($data, $unit)
    {
        $components = $this->getUnitFieldComponents($unit);
        $field      = explode('.', $components['name']);

        $row   = (int) $field[1];
        $cell  = (int) $field[2];
        $field = $field[0];

        // If the table row isn't created yet, create it.
        if (empty($data[$field][$row])) {
            $data[$field][$row] = ['cells' => []];
        }

        // Add the cell value to the correct cell.
        $data[$field][$row]['cells'][$cell] = $unit['target'];

        // Remove the old value.
        unset($data[$components['name']]);

        return $data;
    }

    /**
     * Applies an array value and removes the old string value from the data.
     *
     * @param  array $data
     * @param  array $unit
     * @return array
     */
    private function applyArrayValue($data, $unit)
    {
        $components = $this->getUnitFieldComponents($unit);
        $name       = explode('.', $components['name']);

        // Apply the correct array value.
        $data[$name[0]][$name[1]] = $unit['target'];

        // Remove the old value.
        unset($data[$name[0] . '.' . $name[1]]);

        return $data;
    }

    /**
     * Returns the field components stored in the field string, name and type.
     *
     * @param  array $unit
     * @return array
     */
    private function getUnitFieldComponents($unit)
    {
        $field = explode(':', $unit['field']);

        return [
            'type' => (!empty($field[1])) ? $field[1] : 'string',
            'name' => $field[0],
        ];
    }
}
