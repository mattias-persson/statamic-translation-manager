<?php

namespace Statamic\Addons\TranslationManager\Exporting;

use Statamic\Addons\TranslationManager\Helpers\Config;

class FileZipper
{
    /**
     * Zips files and returns the path to the zipped file.
     *
     * @param array $files
     * @return string
     */
    public static function zip($files)
    {
        $zipname = Config::get('export_path').'translations-'.date('Y-m-d-His').'.zip';

        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE);

        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();

        // Remove the single .xlf-files.
        foreach ($files as $file) {
            unlink($file);
        }

        return $zipname;
    }
}
