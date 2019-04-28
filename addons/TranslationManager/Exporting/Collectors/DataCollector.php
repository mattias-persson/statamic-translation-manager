<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

class DataCollector
{
    protected $options;

    /**
     * The available collectors.
     * This array determines what datatypes will be collected.
     *
     * @var array
     */
    protected $collectors = [
        'page' => PageCollector::class,
        'global' => GlobalCollector::class,
        'collection' => CollectionCollector::class,
    ];

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function collect()
    {
        $data = collect();

        foreach ($this->collectors as $key => $collector) {
            if ($this->options[$key] === 'no') {
                continue;
            }

            if ($this->options[$key] === 'all') {
                $data = $data->merge(app($collector)->all());
            } else {
                $item = app($collector)->find($this->options[$key]);

                // If the returned value is a collection, for example
                // multiple entries in a selected collection set, add
                // all of them to the data. Otherwise, just push the one.
                if (class_basename($item) === 'EntryCollection') {
                    foreach ($item as $object) {
                        $data = $data->push($object);
                    }
                } else {
                    $data = $data->push($item);
                }
            }
        }

        return $data;
    }
}
