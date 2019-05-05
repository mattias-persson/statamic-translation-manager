<?php

namespace Statamic\Addons\TranslationManager\Helpers;

class Config
{
    /**
     * Returns a config value.
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        return self::config()[$key] ?? null;
    }

    /**
     * All config values.
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'export_path' => dirname(__FILE__).'/../Exporting/exports/',
        ];
    }
}
