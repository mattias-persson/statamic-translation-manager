<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors;

abstract class BaseCollector
{
    abstract public function all();

    abstract public function find($handle);
}
