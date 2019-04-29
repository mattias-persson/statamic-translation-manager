<?php

namespace Statamic\Addons\TranslationManager\Exporting\Collectors\Contracts;

interface Collector
{
    public function all($config);

    public function find($handle);
}
