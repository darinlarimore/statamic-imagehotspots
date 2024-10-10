<?php

namespace Darinlarimore\StatamicImagehotspots\Fieldtypes;

use Statamic\Fields\Fieldtype;
use Statamic\Facades\Image;
use Statamic\Facades\Asset;

class ImageHotSpots extends Fieldtype
{
    /**
     * The blank/default value.
     *
     * @return array
     */
    public function defaultValue()
    {
        return null;
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param  mixed  $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    public function preload()
    {

        $data = [];

        $imageFieldHandle = $this->config('imageFieldHandle');

        if ($imageFieldHandle === null) {
            return $data;
        }

        // Find value of the nested array key with the same handle of the image field
        $imageUrl = collect($this->field->parent()->blocks)->pluck($imageFieldHandle)->filter()->flatten()->first();

        if ($imageUrl === null) {
            return $data;
        }

        $image = Asset::find($imageUrl);

        if ($imageUrl === null) {
            return $data;
        }

        if ($image->extension() === 'svg') {
            $data['image'] = $image->url();
            return $data;
        }

        $data['image'] = Image::manipulate($image)->params(['w' => 800])->build();

        return $data;
    }

    /**
     * Process the data before it gets saved.
     *
     * @param  mixed  $data
     * @return array|mixed
     */
    public function process($data)
    {
        return $data;
    }

    protected $icon = 'add-circle';

    public $categories = ['media'];

    protected function configFieldItems(): array
    {
        return [
            'imageFieldHandle' => [
                'display' => 'Image Field Handle',
                'instructions' => 'The handle of the accompanying image field from which the image will be loaded.',
                'required' => true,
                'type' => 'slug',
                'width' => 50
            ],
        ];
    }
}
