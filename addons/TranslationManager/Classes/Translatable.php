<?php

namespace Statamic\Addons\TranslationManager\Classes;

use Statamic\Addons\TranslationManager\Classes\Traits\Cleaner;
use Statamic\API\Fieldset;

class Translatable
{
    use Cleaner;

    /**
     * Prepares the page data for being translated.
     *
     * @param  Statamic\API\Page $page
     * @return array
     */
    public function prepareData($page)
    {
        $data = $this->takeStringValues($page);
        $data = $this->removeUntranslatableFields($data);

        return $data;
    }

    /**
     * Breaks the page content down into string values,
     * rather than a mix of strings and arrays.
     *
     * @param  Statamic\API\Page $page
     * @return array
     */
    private function takeStringValues($page)
    {
        $data          = $page->data();
        $className     = get_class($page);

        $processedData = [];

        foreach ($data as $key => $value) {
            $field = $this->getFieldInfo($page, $key);

            // Make sure the field is localizable. Otherwise, skip it.
            // Page titles does not seem to be included.
            if ((empty($field['localizable']) || $field['localizable'] !== true)) {
                if (!($key === 'title' && in_array($className, ['Statamic\Data\Pages\Page', 'Statamic\Data\Entries\Entry', 'Statamic\Data\Taxonomies\Term']))) {
                    continue;
                }
            }

            $fieldtype = $this->getFieldType($page, $key);

            // If the value is already a string, simply apply it. This prevents trying
            // to format array values on strings when importing a file, since these values
            // were already formatted to strings when exporting the file.
            if (is_string($value)) {
                $processedData[$key] = $value;

                continue;
            }

            switch ($fieldtype) {
                // Untranslatable fields. These do not include the actual label in the
                // page data.
                case 'suggest':
                case 'radio':
                case 'checkboxes':
                    continue;
                    break;

                case 'array':
                case 'collection':
                case 'list':
                case 'tags':
                case 'checkbox':
                    $processedData = $this->handleArrayValues($value, $key, $processedData);
                    break;

                case 'replicator':
                    $processedData = $this->handleReplicatorValues($value, $key, $processedData);
                    break;

                case 'table':
                    $processedData = $this->handleTableValues($value, $key, $processedData);
                    break;

                case 'bard':
                    $processedData = $this->handleBardValues($value, $key, $processedData);
                    break;

                // Regular string values.
                default:
                    $processedData[$key] = $value;
                    break;
            }
        }

        return $processedData;
    }

    /**
     * Breaks down array values into strings that can be translated.
     *
     * @param  array $array
     * @param  string $key
     * @param  array $data
     * @return array
     */
    private function handleArrayValues($array, $key, $data)
    {
        foreach ($array as $index => $string) {
            $data[$key . '.' . $index] = $string;
        }

        return $data;
    }

    /**
     * Breaks down replicator values into strings that can be translated.
     *
     * @param  array $replicator
     * @param  string $key
     * @param  array $data
     * @return array
     */
    private function handleReplicatorValues($replicator, $key, $data)
    {
        foreach ($replicator as $rowIndex => $set) {
            foreach ($set as $setKey => $value) {
                $data[$key . '.' . $rowIndex . '.' . $setKey] = $value;
            }
        }

        return $data;
    }

    /**
     * Breaks down table values into strings that can be translated.
     *
     * @param  array $table
     * @param  string $key
     * @param  array $data
     * @return array
     */
    private function handleTableValues($table, $key, $data)
    {
        foreach ($table as $rowIndex => $cells) {
            foreach ($cells as $cell) {
                foreach ($cell as $cellIndex => $string) {
                    $data[$key . '.' . $rowIndex . '.' . $cellIndex] = $string;
                }
            }
        }

        return $data;
    }

    /**
     * Breaks down bard values into strings that can be translated.
     *
     * @param  array $bard
     * @param  string $key
     * @param  array $data
     * @return array
     */
    private function handleBardValues($bard, $key, $data)
    {
        foreach ($bard as $rowIndex => $set) {
            foreach ($set as $setKey => $value) {
                $data[$key . '.' . $rowIndex . '.' . $setKey] = $value;
            }
        }

        return $data;
    }

    /**
     * Returns field type information about a field.
     *
     * @param  Statamic\API\Page $page
     * @param  string $field
     * @return mixed
     */
    private function getFieldInfo($page, $field)
    {
        $defaultLocale = $page->locales()[0];

        if (in_array(get_class($page), ['Statamic\Data\Entries\Entry'])) {
            try {
                $fieldset = Fieldset::get($page->in($defaultLocale)->collection()->get('fieldset'))->contents();
            } catch (\Exception $e) {
                $fieldset = Fieldset::get($page->in($defaultLocale)->get('fieldset'))->contents();
            }
        } else {
            try {
                $fieldset = Fieldset::get($page->in($defaultLocale)->get('fieldset'))->contents();
            } catch (\Exception $e) {
                $fieldset = Fieldset::get($page->in($defaultLocale)->collection()->get('fieldset'))->contents();
            }
        }

        // Arrays are formatted as field.index. We only want the field name.
        $field = explode('.', $field)[0];

        if (!empty($fieldset['fields'][$field])) {
            return $fieldset['fields'][$field];
        }

        return null;
    }

    /**
     * Returns the type of the current field.
     *
     * @param  Statamic\API\Page $page
     * @param  string $field
     * @return string
     */
    public function getFieldType($page, $field)
    {
        $field = $this->getFieldInfo($page, $field);

        try {
            return (!empty($field)) ? $field['type'] : 'string';
        } catch (\Exception $error) {
            return 'string';
        }
    }
}
