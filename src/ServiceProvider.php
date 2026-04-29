<?php

namespace Darinlarimore\StatamicImagehotspots;

use Darinlarimore\StatamicImagehotspots\Fieldtypes\ImageHotSpots;
use Darinlarimore\StatamicImagehotspots\GraphQL\HotImageType;
use Darinlarimore\StatamicImagehotspots\GraphQL\HotspotType;
use Darinlarimore\StatamicImagehotspots\GraphQL\ImageHotSpotsType;
use Darinlarimore\StatamicImagehotspots\Tags\HotSpotImageTag;
use Statamic\Facades\GraphQL;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/main.js',
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
