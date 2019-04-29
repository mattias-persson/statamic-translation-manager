<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators;

use Statamic\API\URL;
use Statamic\Addons\TranslationManager\Helpers\Locale;

class DataPreparator
{
    /**
     * The field preparator class.
     *
     * @var FieldPreparator
     */
    protected $fieldPreparator;

    /**
     * The available translation languages.
     * Each language will generate 1 file.
     *
     * @var array
     */
    protected $locales;

    /**
     * Initiate the preparator.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->fieldPreparator = new FieldPreparator;
        $this->locales = Locale::collect($options['locale'])->flatMap(function ($locale) {
            return [$locale['code'] => []];
        });
    }

    /**
     * Organize the collected data into a structure that we can export.
     *
     * @param Collection $data
     * @return Collection
     */
    public function prepare($data)
    {
        return $this->splitIntoLocales($data)->map(function ($items, $locale) {
            // An item can be a Page, a Global, a Collection Entry etc...
            foreach ($items as $index => $item) {
                $items[$index] = [
                    'meta' => [
                        'id' => $item->id(),
                        'type' => class_basename($item->original),
                        'url' => URL::prependSiteUrl($item->original->uri(), $item->locale()),
                        'uri' => $item->locale().$item->original->uri(),
                        'source-language' => Locale::default(),
                        'target-language' => $locale,
                    ],
                    'fields' => $this->fieldPreparator->prepare($item),
                ];
            }

            return $items;
        });
    }

    /**
     * Splits the data into chunks organized by locale.
     *
     * @param Collection $data
     * @return Collection
     */
    protected function splitIntoLocales($data)
    {
        return $this->locales->map(function ($value, $locale) use ($data) {
            $value = [];

            foreach ($data as $item) {
                $localizedItem = $item->in($locale);
                $localizedItem->original = $item;

                $value[] = $localizedItem;
            }

            return $value;
        });
    }
}
