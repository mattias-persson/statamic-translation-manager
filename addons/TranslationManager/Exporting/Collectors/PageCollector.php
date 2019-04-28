<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

use Statamic\API\Page;

class PageCollector extends BaseCollector
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
