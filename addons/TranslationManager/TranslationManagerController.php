<?php

namespace Statamic\Addons\TranslationManager;

use Statamic\API\Page;
use Statamic\API\Taxonomy;
use Statamic\API\GlobalSet;
use Illuminate\Http\Request;
use Statamic\API\Collection;
use Statamic\Extend\Controller;
use Statamic\Addons\TranslationManager\Helpers\Locale;
use Statamic\Addons\TranslationManager\Exporting\Exporter;

class TranslationManagerController extends Controller
{
    /**
     * Maps to your route definition in routes.yaml
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view('index');
    }

    public function getImport()
    {
    }

    /**
     * Display the export form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getExport()
    {
        return $this->view('export')
            ->with('actionUrl', $this->actionUrl('export'))
            ->with('pages', Page::all())
            ->with('globals', GlobalSet::all())
            ->with('collections', Collection::all())
            ->with('taxonomies', Taxonomy::all())
            ->with('locales', Locale::get());
    }

    /**
     * Run the export.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postExport(Request $request)
    {
        $exporter = new Exporter($this->getConfig(), $request->all());

        return response()->download($exporter->run());
    }
}
