<?php

namespace Statamic\Addons\TranslationManager\Classes;

use Statamic\API\Config;

class Locales
{
    /**
     * Returns translatable locales.
     *
     * @return array
     */
    public static function getTranslatableLocales()
    {
        $locales = Config::getLocales();

        // Don't translate the default locale.
        unset($locales[0]);

        return $locales;
    }

    /**
     * Returns translatable full locales.
     *
     * @return array
     */
    public static function getTranslatableFullLocales()
    {
        $fullLocales = null;
        $locales     = Locales::getTranslatableLocales();
        foreach ($locales as $locale) {
            $fullLocales[] = Config::getFullLocale($locale);
        }

        return $fullLocales;
    }

    /**
     * Returns translatable locale names.
     *
     * @return array
     */
    public static function getTranslatableLocaleNames()
    {
        $localeNames = null;
        $locales     = Locales::getTranslatableLocales();
        foreach ($locales as $locale) {
            $localeNames[] = Config::getLocaleName($locale);
        }

        return $localeNames;
    }

    /**
     * Returns the selected locale or all translatable locales if none is selected.
     *
     * @param  string $locale
     * @return array
     */
    public static function collectLocales($locale = null)
    {
        if (empty($locale) || !$locale) {
            return Locales::getTranslatableLocales();
        }

        return [$locale];
    }
}
