<?php

namespace Darinlarimore\StatamicImagehotspots\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Facades\Image;
use Statamic\Facades\Asset;


class AssetController extends CpController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Don't load if the control panel is disabled
        if (! config('statamic.cp.enabled')) {
            return null;
        }

        $image = Asset::find($request->getContent());

        if ($image === null) {
            return null;
        }

        if ($image->extension() === 'svg') {
            return response()->json($image->url());
        }

        return response()->json(Image::manipulate($image)->params(['w' => 800])->build());
    }
}
