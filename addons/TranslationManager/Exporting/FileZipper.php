<?php

namespace Statamic\Addons\TranslationManager\Exporting;

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
        $zipname = dirname(__FILE__) . '/exports/translations-'.date('Y-m-d-His').'.zip';

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
