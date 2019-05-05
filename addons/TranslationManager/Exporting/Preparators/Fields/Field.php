<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators\Fields;

abstract class Field
{
    /**
     * The processed fields to which we want to add the data.
     *
     * @var array
     */
    protected $fields;

    /**
     * Apply the fields to the field mapper.
     *
     * @param array $fields
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Parse and add the current field to the list of fields.
     *
     * @param array $data
     * @return array
     */
    abstract public function map($data);
}
