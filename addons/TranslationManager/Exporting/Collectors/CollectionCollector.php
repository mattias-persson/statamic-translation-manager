<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

use Statamic\API\Entry;
use Statamic\API\Collection;

class CollectionCollector extends BaseCollector
{
    /**
     * Retrieves all entries in all collections.
     *
     * @return Collection
     */
    public function all()
    {
        $collections = Collection::handles();
        $entries = collect();

        foreach ($collections as $handle) {
            $items = Entry::whereCollection($handle);

            foreach ($items as $entry) {
                $entries = $entries->push($entry);
            }
        }

        return $entries;
    }

    /**
     * Returns all entries in the selected collection.
     *
     * @param string $handle
     * @return Collection
     */
    public function find($handle)
    {
        return Entry::whereCollection($handle);
    }
}
