<?php

namespace Statamic\Addons\TranslationManager\Classes\Traits;

trait Cleaner
{
    /**
     * The fields that will be excluded from imports or exports file.
     *
     * @var array
     */
    public $dontTranslate = ['id', 'fieldset', 'template'];

    /**
     * Trims out the fields that shouldn't be translated from the data.
     *
     * @param  array $data
     * @return array
     */
    public function removeUntranslatableFields($data)
    {
        if (empty($this->dontTranslate)) {
            return $data;
        }

        // Remove all fields specified in the dontTranslate-array.
        foreach ($this->dontTranslate as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }

        // Find values that have an untranslatable format and remove them.
        foreach ($data as $key => $value) {
            if (!is_string($value) || !$this->isTranslatable($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Checks whether a value in the data has a translatable format or not.
     *
     * @param  string  $value
     * @return boolean
     */
    private function isTranslatable(String $value)
    {
        return (!$this->isIdField($value) && $this->hasLetters($value));
    }

    /**
     * Checks whether a string is a Statamic ID.
     *
     * @param  string  $value
     * @return boolean
     */
    private function isIdField(String $value)
    {
        if (strlen($value) !== 36) {
            return false;
        }

        return in_array('-', [$value[8], $value[13], $value[18], $value[23]]);
    }

    /**
     * Checks whether a string has actual letters or not.
     *
     * @param  String  $value
     * @return boolean
     */
    private function hasLetters(String $value)
    {
        return preg_match('/[a-z]/i', $value);
    }
}
