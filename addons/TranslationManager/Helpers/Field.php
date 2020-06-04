<?php

namespace Statamic\Addons\TranslationManager\Helpers;

use Statamic\API\Fieldset;

class Field
{
    /**
     * The field.
     *
     * @var array
     */
    protected $field;

    /**
     * Retrieve the field information.
     *
     * @param object $item
     * @param string $field
     */
    public function __construct($item, $field)
    {
        $this->field = $this->getField($item, $field);
    }

    /**
     * When accessing the string version of the class, return the field type.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->field['type'];
    }

    /**
     * Returns an attribute value on the field.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->field[$key] ?? null;
    }

    /**
     * Determines whether the field is valid and localizable.
     *
     * @return bool
     */
    public function shouldBeTranslated()
    {
        return $this->field && !empty($this->field['type']) && !empty($this->field['localizable']);
    }

    /**
     * Retrieves the field information.
     *
     * @param object $item
     * @param string $field
     * @return array|null
     */
    protected function getField($item, $field)
    {
        if (in_array(class_basename($item->original), ['Entry'])) {
            try {
                $fieldset = Fieldset::get($item->original->collection()->get('fieldset'))->contents();
            } catch (\Exception $e) {
                $fieldset = Fieldset::get($item->original->get('fieldset'))->contents();
            }
        } elseif (in_array(class_basename($item->original), ['Term'])) {
            try {
                $fieldsetName = $item->original->taxonomy()->get('fieldset');

                if (!$fieldsetName) {
                    if (is_string($item->get($field))) {
                        return ['type' => 'text', 'localizable' => true];
                    }
                }

                $fieldset = Fieldset::get($fieldsetName)->contents();
            } catch (\Exception $e) {
                if (is_string($item->get($field))) {
                    return ['type' => 'text', 'localizable' => true];
                }
            }
        } else {
            try {
                $fieldset = Fieldset::get($item->original->get('fieldset'))->contents();
            } catch (\Exception $e) {
                if (!method_exists($item->original, 'collection')) {
                    return;
                }

                $fieldset = Fieldset::get($item->original->collection()->get('fieldset'))->contents();
            }
        }

        // Arrays are formatted as field.index. We only want the field name.
        $field = explode('.', $field)[0];

        if (isset($fieldset['sections'])) {
            $fieldset['fields'] = collect($fieldset['sections'])->flatMap(function ($section) {
                return $section['fields'] ?? [];
            })->toArray();
        }

        // Merge 'partial' fieldtypes into fields array
        $fieldset['fields'] = collect($fieldset['fields'])->flatMap(function ($field, $key) {
            if ($field['type'] === 'partial') {
                return Fieldset::get($field['fieldset'])->contents()['fields'];
            }

            return [$key => $field];
        })->toArray();

        return $fieldset['fields'][$field] ?? null;
    }
}
