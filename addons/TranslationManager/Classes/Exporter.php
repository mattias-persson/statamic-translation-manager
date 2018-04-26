<?php

namespace Statamic\Addons\TranslationManager\Classes;

use Statamic\API\GlobalSet;
use Statamic\API\Collection;
use Statamic\API\Taxonomy;
use Statamic\API\Term;
use Statamic\API\Page;
use Statamic\API\Entry;
use Statamic\API\Config;

class Exporter extends Translatable
{
    /**
     * The path where the exported files will reside.
     *
     * @var string
     */
    protected $exportPath;

    /**
     * The pages that will be exported.
     *
     * @var array
     */
    protected $pages;

    /**
     * The globals that will be exported.
     *
     * @var array
     */
    protected $globals;

    /**
     * The collections that will be exported.
     *
     * @var array
     */
    protected $collections;

    /**
     * The taxonomies that will be exported.
     *
     * @var array
     */
    protected $taxonomies;

    /**
     * The locales that will be translated.
     *
     * @var array
     */
    protected $locales;

    /**
     * The config for the addon.
     *
     * @var array
     */
    protected $config;

    public function __construct($config)
    {
        $this->exportPath = dirname(__FILE__) . '/../exports/';
        $this->config     = $config;

        if (is_string($this->config['exclude_page_ids'])) {
            $this->config['exclude_page_ids'] = explode(',', $this->config['exclude_page_ids']);
        }

        if (is_string($this->config['exclude_collection_slugs'])) {
            $this->config['exclude_collection_slugs'] = explode(',', $this->config['exclude_collection_slugs']);
        }
    }

    /**
     * Runs the export.
     *
     * @param  array $args
     * @return string
     */
    public function run($args = [])
    {
        // Clear out the result directory.
        $this->clearExportsDirectory();

        $this->locales     = $this->getLocalesToExport($args['locale']);
        $this->pages       = $this->getPagesToExport($args['page']);
        $this->globals     = $this->getGlobalsToExport($args['global']);
        $this->collections = $this->getCollectionsToExport($args['collection']);
        $this->taxonomies  = $this->getTaxonomiesToExport($args['taxonomy']);

        $filepaths = [];

        // Export each locale.
        foreach ($this->locales as $locale) {
            $filepaths[] = $this->exportLocale($locale);
        }

        // Zip files for download if more than one, otherwise return the single file path.
        return $this->zipFiles($filepaths);
    }

    /**
     * Exports all pages to the given locale.
     *
     * @param  string $locale
     * @param  array $pages
     * @return string
     */
    private function exportLocale($locale)
    {
        $files = [];

        if (!empty($this->pages)) {
            foreach ($this->pages as $page) {
                $files[] = $this->export('page', $page, $locale);
            }
        }

        if (!empty($this->globals)) {
            foreach ($this->globals as $global) {
                $files[] = $this->export('global', $global, $locale);
            }
        }

        if (!empty($this->collections)) {
            foreach ($this->collections as $collection) {
                if (!in_array($collection->path(), $this->config['exclude_collection_slugs'])) {
                    foreach (Entry::whereCollection($collection->path()) as $entry) {
                        $files[] = $this->export('entry', $entry, $locale);
                    }
                }
            }
        }

        if (!empty($this->taxonomies)) {
            foreach ($this->taxonomies as $taxonomy) {
                foreach (Term::whereTaxonomy($taxonomy->path()) as $term) {
                    $files[] = $this->export('term', $term, $locale);
                }
            }
        }

        return $this->generateXliffFile($files, $locale);
    }

    /**
     * Exports a page with the given locale.
     *
     * @param  string $type
     * @param  mixed $page
     * @param  string $locale
     * @return array
     */
    private function export($type, $page, $locale)
    {
        $originalData   = $this->prepareData($page);
        $translatedData = $this->prepareData($page->in($locale));

        $file = [
            'type'            => $type,
            'id'              => $page->get('id'),
            'title'           => $page->get('title'),
            'source-language' => 'en',
            'target-language' => $locale,
            // 'source-language' => 'en_GB',
            // 'target-language' => Config::getFullLocale($locale),
            'units'           => [],
            'uri'             => $page->uri(),
        ];

        // Term handles id in a special way
        // - A term ID is the taxonomy handle and the slug joined by a slash.
        // Since the export format does not allow slash we need to use two dashes
        // to find a unique separator
        if ($type === 'term') {
            $file['id'] = $page->taxonomy()->path() . '__' . $page->slug();
        }

        foreach ($originalData as $key => $original) {
            $name = str_replace(' ', '+', $key) . ':' . $this->getFieldType($page, $key);

            $file['units'][] = [
                'name'   => $name,
                'source' => $original,
                'target' => (!empty($translatedData[$key])) ? $translatedData[$key] : '',
            ];
        }

        return $file;
    }

    /**
     * Generates an xliff file for a page and locale.
     *
     * @param  array $files
     * @param  string $locale
     * @return string
     */
    private function generateXliffFile($files, $locale)
    {
        $xliff = new XliffDocument();

        foreach ($files as $file) {
            // Exclude some pages
            if (in_array($file['id'], $this->config['exclude_page_ids'])) {
                continue;
            }

            $url   = $this->config['page_url'] ? $this->config['page_url'] : env('APP_URL');
            $query = $this->config['page_query_string'] ? $this->config['page_query_string'] : '';

            $xliff->file(true)->setAttributes([
                'source-language' => $file['source-language'],
                'target-language' => $file['target-language'],
                'datatype'        => 'plaintext',
                'original'        => $file['type'] . ':' . $file['id'],
            ])->header(true)
                ->skl(true)
                ->{'external-file'}(true)
                ->setAttribute('href', $url . $file['uri'] . $query);

            $xliff->file()->body(true);

            foreach ($file['units'] as $unit) {
                // Apply the source.
                $xliff->file()
                    ->body()
                    ->unit(true)
                    ->setAttribute('id', $unit['name'])
                    ->source(true)
                    ->setTextContent($unit['source']);

                // Apply the target.
                $xliff->file()->body()->unit()->target(true)->setTextContent($unit['target']);
            }
        }

        $dom = $xliff->toDOM();

        $filename = (count($this->locales) > 1) ? $locale : $this->getFilename();
        $filepath = $this->exportPath . $filename . '.xlf';
        file_put_contents($filepath, $dom->saveXML());

        return $filepath;
    }

    /**
     * Zips the exported files into an archive and removes the single files.
     *
     * @param  array $filepaths
     * @return string
     */
    private function zipFiles($filepaths)
    {
        // If just 1 file, don't zip it, return the filepath instead.
        if (count($filepaths) === 1) {
            return $filepaths[0];
        }

        $zipname = $this->exportPath . $this->getFilename() . '.zip';

        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE);

        // Add the file to the zip archive.
        foreach ($filepaths as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();

        // Remove the single .xlf-files.
        foreach ($filepaths as $file) {
            unlink($file);
        }

        return $zipname;
    }

    /**
     * Returns the filename for the file.
     *
     * @return string
     */
    private function getFilename()
    {
        $name = 'translations-';
        $name .= (count($this->locales) > 1) ? 'all-languages' : $this->locales[0];
        $name .= '-' . date('Y-m-d-His');

        return $name;
    }

    /**
     * Returns the selected locale or all locales.
     *
     * @param  mixed $selection
     * @return array
     */
    private function getLocalesToExport($selection)
    {
        if ($selection === 'all') {
            $selection = null;
        }

        return Locales::collectLocales($selection);
    }

    /**
     * Returns the selected page, all pages or no pages based on selection.
     *
     * @param  mixed $selection
     * @return array
     */
    private function getPagesToExport($selection)
    {
        if ($selection === 'all') {
            return Page::all();
        } elseif ($selection === 'no') {
            return [];
        } else {
            return [Page::find($selection)];
        }
    }

    /**
     * Returns the selected global, all globals or no globals based on selection.
     *
     * @param  mixed $selection
     * @return array
     */
    private function getGlobalsToExport($selection)
    {
        if ($selection === 'all') {
            return GlobalSet::all();
        } elseif ($selection === 'no') {
            return [];
        } else {
            return [GlobalSet::find($selection)];
        }
    }

    /**
     * Returns the selected collection, all collections or no collections based on selection.
     *
     * @param  mixed $selection
     * @return array
     */
    private function getCollectionsToExport($selection)
    {
        if ($selection === 'all') {
            return Collection::all();
        } elseif ($selection === 'no') {
            return [];
        } else {
            return [Collection::whereHandle($selection)];
        }
    }

    /**
     * Returns the selected taxonomy, all taxonomies or no taxonomies based on selection.
     *
     * @param  mixed $selection
     * @return array
     */
    private function getTaxonomiesToExport($selection)
    {
        if ($selection === 'all') {
            return Taxonomy::all();
        } elseif ($selection === 'no') {
            return [];
        } else {
            return [Taxonomy::whereHandle($selection)];
        }
    }

    /**
     * Removes all files from the export directory to make room for the new file.
     *
     * @return void
     */
    private function clearExportsDirectory()
    {
        $files       = scandir($this->exportPath);
        $filesToKeep = ['.', '..', '.DS_Store', '.gitkeep'];

        foreach ($files as $file) {
            if (!in_array($file, $filesToKeep)) {
                unlink($this->exportPath . $file);
            }
        }
    }
}
