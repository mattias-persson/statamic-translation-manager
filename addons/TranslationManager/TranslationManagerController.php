<?php

namespace Statamic\Addons\TranslationManager;

use Illuminate\Http\Request;
use Statamic\Addons\TranslationManager\Classes\Locales;
use Statamic\Addons\TranslationManager\Classes\Stats;
use Statamic\Addons\TranslationManager\Classes\Exporter;
use Statamic\Addons\TranslationManager\Classes\Importer;
use Statamic\API\GlobalSet;
use Statamic\API\Collection;
use Statamic\API\Taxonomy;
use Statamic\API\Page;
use Statamic\Extend\Controller;

class TranslationManagerController extends Controller
{
    /**
     * Maps to your route definition in routes.yaml
     *
     * @return Illuminate\Http\Response
     */
    public function index(Stats $stats)
    {
        return $this->view('index')
            ->with('locales', Locales::getTranslatableLocales())
            ->with('fullLocales', Locales::getTranslatableFullLocales())
            ->with('localeNames', Locales::getTranslatableLocaleNames())
            ->with('stats', $stats->strings());
    }

    /**
     * Returns the view containing the import form.
     *
     * @return Illuminate\Http\Response
     */
    public function getImport()
    {
        return $this->view('import')->with('actionUrl', $this->actionUrl('import'));
    }

    /**
     * Returns the view containing the export form.
     *
     * @return Illuminate\Http\Response
     */
    public function getExport()
    {
        return $this->view('export')
            ->with('actionUrl', $this->actionUrl('export'))
            ->with('pages', Page::all())
            ->with('globals', GlobalSet::all())
            ->with('collections', Collection::all())
            ->with('taxonomies', Taxonomy::all())
            ->with('locales', Locales::getTranslatableLocales());
    }

    /**
     * Runs the import.
     *
     * @param  Illuminate\Http\Request $request
     * @param  Statamic\Addons\TranslationManager\Classes\Importer $importer
     * @return Illuminate\Http\Response
     */
    public function postImport(Request $request, Importer $importer)
    {
        $this->validate($request, ['file' => 'required|mimes:xml,xlf,xliff']);

        $importer->run($request->file);

        return back()->with('success', 'The file was imported!');
    }

    /**
     * Runs the export.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postExport(Request $request)
    {
        $exporter = new Exporter($this->getConfig());

        return response()->download($exporter->run($request->all()));
    }
}
