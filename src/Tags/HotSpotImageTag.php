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

            if (! $field) {
                return '';
            }

            $contextValue = $this->context->get($field);

            // Handle both field objects and direct data arrays
            if (is_object($contextValue) && method_exists($contextValue, 'value')) {
                // This is a field object (page-level field)
                $data = $contextValue->value();
            } else {
                // This is direct data (global field reference)
                $data = $contextValue;
            }

            if (! $data || ! is_array($data)) {
                return '';
            }

            return [
                'image' => $data['imageFile'] ?? null,
                'hotspots' => $data['hotspots'] ?? [],
            ];

        } catch (\Exception $e) {
            return '';
        }
    }
}
