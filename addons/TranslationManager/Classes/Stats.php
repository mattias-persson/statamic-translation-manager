<?php

namespace Statamic\Addons\TranslationManager\Classes;

use Statamic\API\GlobalSet;
use Statamic\API\Page;

class Stats extends Translatable
{
    /**
     * Returns stats about translated strings.
     *
     * @return array
     */
    public function strings()
    {
        $counters = ['total' => 0, 'translated' => 0];
        $counters = $this->countTranslatedStrings(Page::all(), $counters);
        $counters = $this->countTranslatedStrings(GlobalSet::all(), $counters);

        return $counters;
    }

    /**
     * Counts the amount of translated and total strings in a collection.
     *
     * @param  array $collection
     * @param  array $counters
     * @return array
     */
    private function countTranslatedStrings($collection, $counters)
    {
        $locales = Locales::getTranslatableLocales();

        foreach ($collection as $item) {
            $translatableStrings = $this->prepareData($item);

            foreach ($locales as $locale) {
                // Add the string count for all locales to the total count.
                $counters['total'] += count($translatableStrings);

                // Add the translated strings.
                $translated = $this->prepareData($item->in($locale));
                $diff       = $this->getFullDiff($translated, $translatableStrings);

                if (!empty($diff)) {
                    $count = count(array_diff_assoc($translated, $translatableStrings));
                    $counters['translated'] += $count;
                }
            }

            // Add the string count for the default locale to the total count.
            $counters['total'] += count($translatableStrings);
        }

        return $counters;
    }

    /**
     * Returns the complete difference between 2 arrays.
     *
     * @param  array $array1
     * @param  array $array2
     * @return array
     */
    private function getFullDiff($array1, $array2)
    {
        $diff1 = array_diff($array1, $array2);
        $diff2 = array_diff($array2, $array1);

        return array_merge($diff1, $diff2);
    }
}
