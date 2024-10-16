<?php

namespace Darinlarimore\StatamicImagehotspots;

use Statamic\Providers\AddonServiceProvider;
use Darinlarimore\StatamicImagehotspots\Fieldtypes\ImageHotSpots;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/main.js',
            'resources/css/main.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon()
    {
        ImageHotSpots::register();
    }
}
