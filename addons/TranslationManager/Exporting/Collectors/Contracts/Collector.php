<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors\Contracts;

interface Collector
{
    public function all();

    public function find($handle);
}
