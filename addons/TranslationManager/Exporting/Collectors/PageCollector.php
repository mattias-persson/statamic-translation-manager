<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

use Statamic\API\Page;
use Statamic\Addons\TranslationManager\Exporting\Collectors\Contracts\Collector;

class PageCollector implements Collector
{
    public function all()
    {
        return Page::all();
    }

    public function find($handle)
    {
        return Page::find($handle);
    }
}
