<?php

namespace Darinlarimore\StatamicImagehotspots;

use Statamic\Providers\AddonServiceProvider;
use Darinlarimore\StatamicImagehotspots\Fieldtypes\ImageHotSpots;
use Darinlarimore\StatamicImagehotspots\Tags\HotSpotImageTag;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/main.js',
            'resources/css/main.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    // Register tags
    protected $tags = [
        HotSpotImageTag::class,
    ];

    public function bootAddon()
    {
        ImageHotSpots::register();
    }
}
