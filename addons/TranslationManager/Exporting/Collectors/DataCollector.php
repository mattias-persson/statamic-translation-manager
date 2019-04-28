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
                $data->push(app($collector)->find($this->options[$key]));
            }
        }

        return $data;
    }
}
