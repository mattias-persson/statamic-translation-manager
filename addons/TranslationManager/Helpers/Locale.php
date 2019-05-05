<?php

namespace Statamic\Addons\TranslationManager\Helpers;

use Statamic\API\Config;

class Locale
{
    /**
     * Returns a collection of translatable locales.
     *
     * @return Collection
     */
    public static function get()
    {
        $locales = self::all();

        // Don't translate the default locale.
        unset($locales[0]);

        return $locales;
    }

    /**
     * Returns a collection of all locales.
     *
     * @return Collection
     */
    public static function all()
    {
        $locales = Config::getLocales();

        foreach ($locales as $index => $locale) {
            $locales[$index] = [
                'code' => $locale,
                'name' => Config::getLocaleName($locale),
            ];
        }

        return collect($locales);
    }

    /**
     * Returns a single locale based on given code.
     *
     * @param string $code
     * @return Locale
     */
    public static function find($code)
    {
        return self::get()->where('code', $code)->first();
    }

    /**
     * Returns a collection of all translatable locales or a single one if
     * a selection is made.
     *
     * @param string $selection
     * @return Collection
     */
    public static function collect($selection)
    {
        if ($selection === 'all') {
            return self::get();
        }

        return collect([self::find($selection)]);
    }

    /**
     * Returns the default locale code.
     *
     * @return string
     */
    public static function default()
    {
        return Config::getDefaultLocale();
    }
}
