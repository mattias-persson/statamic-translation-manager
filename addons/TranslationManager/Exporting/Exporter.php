<?php

namespace Statamic\Addons\TranslationManager\Exporting;

use Statamic\Addons\TranslationManager\Exporting\Exporters\Xliff;
use Statamic\Addons\TranslationManager\Exporting\Collectors\DataCollector;
use Statamic\Addons\TranslationManager\Exporting\Preparators\DataPreparator;

class Exporter
{
    protected $config;

    public function __construct($config, $options)
    {
        $this->config = $this->parseConfig($config);
        $this->dataCollector = new DataCollector($options);
        $this->dataPreparator = new DataPreparator($options);
    }

    public function run()
    {
        $data = $this->dataCollector->collect();
        $data = $this->dataPreparator->prepare($data);

        return (new Xliff)->create($data);
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
}
