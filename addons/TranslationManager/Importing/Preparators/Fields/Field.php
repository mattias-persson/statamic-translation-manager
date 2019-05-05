<?php

namespace Statamic\Addons\TranslationManager\Importing\Preparators\Fields;

abstract class Field
{
    protected $item;

    /**
     * Apply the translation to the mapper.
     *
     * @param array $translation
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Parse the current translation into an applicable structure.
     *
     * @return array
     */
    abstract public function map($translation);
}
