<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

use Statamic\API\GlobalSet;
use Statamic\Addons\TranslationManager\Exporting\Collectors\Contracts\Collector;

class GlobalCollector implements Collector
{
    /**
     * Returns all global sets.
     *
     * @return Collection
     */
    public function all()
    {
        return GlobalSet::all();
    }

    /**
     * Returns a single global set.
     *
     * @param string|int $handle
     * @return GlobalSet
     */
    public function find($handle)
    {
        return GlobalSet::find($handle);
    }
}
