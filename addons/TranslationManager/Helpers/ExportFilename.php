<?php

namespace Statamic\Addons\TranslationManager\Helpers;

class ExportFilename
{
    public $filename;
    public $filepath;

    public function __construct($data)
    {
        $this->assembleFilename($data);
    }

    protected function assembleFilename($data)
    {
        if (count($data) === 1) {
            $this->filename = $data->keys()->first();
        } else {
            $this->filename = 'translations-all-languages';
        }

        $this->filename .= '-' . date('Y-m-d-His').'.xlf';
        $this->filepath = dirname(__FILE__).'/../Exporting/exports/'.$this->filename;
    }
}
