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
        // The built in file validation doesn't work for some reason, so we have to manually
        // validate the file type.
        if (!in_array($request->file->getClientOriginalExtension(), ['xlf', 'xliff'])) {
            return back()->withErrors(['file' => 'The file must be of the type .xlf or .xliff.']);
        }

        try {
            $data = (new XliffParser(file_get_contents($request->file)))->parse();
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Unable to read the file.');
        }

        try {
            $importer->import($data);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Unable to import the translations.');
        }

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

    protected function errorResponse($e, $message)
    {
        $message .= ' Please visit https://github.com/mattias-persson/statamic-translation-manager/issues/new 
        and open an issue with the following error message included: "'.$e->getMessage().'".';

        return back()->withErrors([
            'file' => $message,
        ]);
    }
}
