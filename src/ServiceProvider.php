<?php

namespace Darinlarimore\StatamicImagehotspots;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\GraphQL;
use Darinlarimore\StatamicImagehotspots\Fieldtypes\ImageHotSpots;
use Darinlarimore\StatamicImagehotspots\Tags\HotSpotImageTag;
use Darinlarimore\StatamicImagehotspots\GraphQL\ImageHotSpotsType;
use Darinlarimore\StatamicImagehotspots\GraphQL\HotImageType;
use Darinlarimore\StatamicImagehotspots\GraphQL\HotspotType;

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

        // Register GraphQL type
       GraphQL::addType(ImageHotSpotsType::class);
       GraphQL::addType(HotImageType::class);
       GraphQL::addType(HotspotType::class);
    }
}
