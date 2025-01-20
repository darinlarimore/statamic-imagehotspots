<?php

namespace Darinlarimore\StatamicImagehotspots\GraphQL;

use Statamic\GraphQL\Types\Fieldtype;
use GraphQL\Type\Definition\Type;
use Statamic\Facades\GraphQL;

class ImageHotSpotsType extends Fieldtype
{
    public const NAME = 'ImageHotSpots';

    protected $attributes = [
        'name' => self::NAME,
    ];

    public function fields(): array
    {
        return [
            'imageFile' => [
                'type' => GraphQL::type(HotImageType::NAME),
                'resolve' => function ($data) {
                    return $data['imageFile'] ?? null;
                },
            ],
            'hotspots' => [
                'type' => Type::listOf(GraphQL::type(HotspotType::NAME)),
                'resolve' => function ($data) {
                    return $data['hotspots'] ?? [];
                },
            ],
        ];
    }
}
