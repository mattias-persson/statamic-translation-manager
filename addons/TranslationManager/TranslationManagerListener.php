<?php

namespace Statamic\Addons\TranslationManager;

use Statamic\API\Nav;
use Statamic\Extend\Listener;

class TranslationManagerListener extends Listener
{
    /**
     * The events to be listened for, and the methods to call.
     *
     * @var array
     */
    public $events = [
        'cp.nav.created' => 'addNavItems',
    ];

    public function addNavItems($nav)
    {
        $translations = Nav::item('Translations')->route('translationmanager.index')->icon('globe');

        $translations->add(function ($item) {
            $item->add(Nav::item('Import')->route('translationmanager.import'));
            $item->add(Nav::item('Export')->route('translationmanager.export'));
        });

        $nav->addTo('tools', $translations);
    }
}
