<?php

namespace Statamic\Addons\TranslationManager\Exporting;

use Statamic\Addons\TranslationManager\Exporting\Exporters\Xliff;
use Statamic\Addons\TranslationManager\Exporting\Collectors\DataCollector;
use Statamic\Addons\TranslationManager\Exporting\Preparators\DataPreparator;

class Exporter
{
    protected $config;

    /**
     * The path where the exported files will be placed before download.
     *
     * @var string
     */
    protected $exportPath;

    public function __construct($config, $options)
    {
        $this->exportPath = dirname(__FILE__) . '/exports/';
        $this->config = $this->parseConfig($config);
        $this->dataCollector = new DataCollector($options);
        $this->dataPreparator = new DataPreparator($options);
    }

    public function run()
    {
        // Clear out the result directory.
        $this->clearExportsDirectory();

        $data = $this->dataCollector->collect();
        $data = $this->dataPreparator->prepare($data);

        $files = [];
        foreach ($data as $locale => $data) {
            $files[] = (new Xliff)->create($locale, $data);
        }

        if (count($files) > 1) {
            return FileZipper::zip($files);
        }

        return $files[0];
    }

    protected function parseConfig($config)
    {
        if (is_string($this->config['exclude_page_ids'])) {
            $config['exclude_page_ids'] = explode(',', $config['exclude_page_ids']);
        }

        if (is_string($this->config['exclude_collection_slugs'])) {
            $config['exclude_collection_slugs'] = explode(',', $config['exclude_collection_slugs']);
        }

        return $config;
    }

    /**
     * Removes all files from the export directory to make
     * room for the new files.
     *
     * @return void
     */
    protected function clearExportsDirectory()
    {
        $files       = scandir($this->exportPath);
        $filesToKeep = ['.', '..', '.DS_Store', '.gitkeep'];

        foreach ($files as $file) {
            if (!in_array($file, $filesToKeep)) {
                unlink($this->exportPath . $file);
            }
        }
    }
}
