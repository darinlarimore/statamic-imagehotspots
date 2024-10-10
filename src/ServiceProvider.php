<?php

namespace Darinlarimore\StatamicImagehotspots;

use Statamic\Providers\AddonServiceProvider;
use Darinlarimore\StatamicImagehotspots\Fieldtypes\ImageHotSpots;
use Darinlarimore\StatamicImagehotspots\Http\Controllers\AssetController;
use Illuminate\Support\Facades\Route;

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

        $this->registerActionRoutes(function () {
            Route::post('asset', AssetController::class)
                ->middleware('statamic.cp.authenticated');
        });
    }
}
