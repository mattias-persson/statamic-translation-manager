<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

use Statamic\API\Page;
use Statamic\Addons\TranslationManager\Exporting\Collectors\Contracts\Collector;

class PageCollector implements Collector
{
    public function all($config)
    {
        return Page::all()->filter(function ($page) use ($config) {
            if (empty($config['exclude_page_ids'])) {
                return true;
            }

            return !in_array($page->id(), explode(',', $config['exclude_page_ids']));
        });
    }

    public function find($handle)
    {
        return Page::find($handle);
    }
}
