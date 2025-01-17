<?php

namespace Darinlarimore\StatamicImagehotspots\Tags;

use Statamic\Tags\Tags;

class HotSpotImageTag extends Tags
{

    protected static $handle = 'image_hot_spots';

    public static function render(...$arguments): string
    {
        return '';
    }

    public function index()
    {
        try {
            $field = $this->params->get('field') ?? null;

            if (!$field) {
                return '';
            }

            $data = $this->context->get($field)->value();

            if (!$data) {
                return '';
            }

            return [
                'image' => $data['imageFile'],
                'hotspots' => $data['hotspots'],
            ];

        } catch (\Exception $e) {
            return '';
        }
    }
}
