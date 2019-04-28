<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

use Statamic\API\GlobalSet;

class GlobalCollector extends BaseCollector
{
    public function all()
    {
        return GlobalSet::all();
    }

    public function find($handle)
    {
        return GlobalSet::find($handle);
    }
}
