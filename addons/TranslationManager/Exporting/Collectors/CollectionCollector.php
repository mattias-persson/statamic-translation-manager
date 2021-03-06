<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

use Statamic\API\Entry;
use Statamic\API\Collection;
use Statamic\Addons\TranslationManager\Exporting\Collectors\Contracts\Collector;

class CollectionCollector implements Collector
{
    /**
     * Retrieves all entries in all collections.
     *
     * @return Collection
     */
    public function all($config)
    {
        $collections = Collection::handles();
        $entries = collect();
        $exclude = !empty($config['exclude_collection_slugs']) ? explode(',', $config['exclude_collection_slugs']) : null;

        foreach ($collections as $handle) {
            if ($exclude && in_array($handle, $exclude)) {
                continue;
            }

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
