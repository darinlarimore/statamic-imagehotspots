<?php

namespace Darinlarimore\StatamicImagehotspots\GraphQL;

use Rebing\GraphQL\Support\Type as GQLType;
use GraphQL\Type\Definition\Type;
use Statamic\GraphQL\Types\ArrayType;
use Statamic\Facades\GraphQL;

class HotspotType extends GQLType
{
    const NAME = "Hotspot";

    protected $attributes = [
        'name' => self::NAME,
    ];

    public function fields(): array
    {
        return [
            'x' => [
                'type' => Type::float(),
            ],
            'y' => [
                'type' => Type::float(),
            ],
            'content' => [
                'type' => GraphQL::type(ArrayType::NAME),
            ],
        ];
    }
}
