<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

use Statamic\API\Term;
use Statamic\API\Taxonomy;
use Statamic\Addons\TranslationManager\Exporting\Collectors\Contracts\Collector;

class TaxonomyCollector implements Collector
{
    /**
     * Retrieves all terms in all taxonomies.
     *
     * @return Collection
     */
    public function all($config)
    {
        $taxonomies = Taxonomy::handles();
        $terms = collect();

        foreach ($taxonomies as $handle) {
            $items = Term::whereTaxonomy($handle);

            foreach ($items as $entry) {
                $terms = $terms->push($entry);
            }
        }

        return $terms;
    }

    /**
     * Returns all terms in the selected taxonomy.
     *
     * @param string $handle
     * @return Collection
     */
    public function find($handle)
    {
        return Term::whereTaxonomy($handle);
    }
}
