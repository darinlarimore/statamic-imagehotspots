<?php

namespace Darinlarimore\StatamicImagehotspots\GraphQL;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GQLType;
use Statamic\Facades\GraphQL;
use Statamic\GraphQL\Types\ArrayType;

class HotspotType extends GQLType
{
    const NAME = 'Hotspot';

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
