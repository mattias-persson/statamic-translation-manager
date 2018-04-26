<?php

namespace Statamic\Addons\TranslationManager\Classes;

use Statamic\API\GlobalSet;
use Statamic\API\Page;
use Statamic\API\Entry;
use Statamic\API\Term;

class Importable
{
    /**
     * The raw file-object containing all the XML data for the item.
     *
     * @var string
     */
    private $raw;

    /**
     * The id.
     *
     * @var string
     */
    private $id;

    /**
     * The type of the item, for example page or global.
     *
     * @var string
     */
    private $type;

    /**
     * The translation units.
     *
     * @var array
     */
    public $units;

    /**
     * The original item.
     *
     * @var mixed
     */
    public $original;

    /**
     * The item in the target language.
     *
     * @var mixed
     */
    public $target;

    /**
     * The constructor.
     *
     * @return void
     */
    public function __construct($raw)
    {
        $this->raw      = $raw;
        $this->id       = $this->getId();
        $this->type     = $this->getType();
        $this->units    = $this->getUnits();
        $this->original = $this->getOriginal();
        $this->target   = $this->getTarget();
    }

    /**
     * Returns the id based on the XML data.
     *
     * @return string
     */
    private function getId()
    {
        $original = XMLReader::parseXmlAttributes($this->raw->attributes())['original'];

        return explode(':', $original)[1];
    }

    /**
     * Returns the type.
     *
     * @return string
     */
    private function getType()
    {
        $original = XMLReader::parseXmlAttributes($this->raw->attributes())['original'];

        return explode(':', $original)[0];
    }

    /**
     * Returns the XML trans-units translated into a managable PHP array.
     *
     * @return array
     */
    private function getUnits()
    {
        return XMLReader::parseXmlUnits($this->raw);
    }

    /**
     * Returns the item to be translated.
     *
     * @return mixed
     */
    private function getTarget()
    {
        $lang = XMLReader::parseXmlAttributes($this->raw->attributes())['target-language'];

        return $this->getOriginal()->in($lang);
    }

    /**
     * Returns the original item.
     *
     * @return mixed
     */
    private function getOriginal()
    {
        $type = $this->getType();

        if ($type === 'page') {
            return Page::find($this->id);
        } elseif ($type === 'global') {
            return GlobalSet::find($this->id);
        } elseif ($type === 'entry') {
            return Entry::find($this->id);
        } elseif ($type === 'term') {
            $this->id = str_replace('__', '/', $this->id);

            return Term::find($this->id);
        }

        return null;
    }
}
