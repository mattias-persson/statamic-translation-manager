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
                if (!method_exists($item->original, 'collection')) {
                    return;
                }

                $fieldset = Fieldset::get($item->original->get('fieldset'))->contents();
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

        if (!empty($fieldset['fields'][$field])) {
            return $fieldset['fields'][$field]['type'] ?? $fieldset['fields'][$field];
        } elseif (!empty($fieldset['sections']['main']['fields'][$field])) {
            return $fieldset['sections']['main']['fields'][$field] ?? $fieldset['sections']['main']['fields'][$field];
        }
    }
}
