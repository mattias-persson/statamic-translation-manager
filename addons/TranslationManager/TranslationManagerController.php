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
use Statamic\Addons\TranslationManager\Importing\Importer;
use Statamic\Addons\TranslationManager\Importing\Parsers\XliffParser;

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

    /**
     * Display the import form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImport()
    {
        return $this->view('import')->with('actionUrl', $this->actionUrl('import'));
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
     * Runs the import.
     *
     * @param  Illuminate\Http\Request $request
     * @param  Statamic\Addons\TranslationManager\Classes\Importer $importer
     * @return Illuminate\Http\Response
     */
    public function postImport(Request $request, Importer $importer)
    {
        // TODO: Validate file.
        #return back()->withErrors(['file' => 'The file must be of the type .xlf or .xliff.']);

        #$this->validate($request, ['file' => 'required|mimes:xml,xlf,xliff,text/xml']);

        $parser = new XliffParser(file_get_contents($request->file));

        $importer->import($parser->parse());


        return back()->with('success', 'The file was imported!');
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
