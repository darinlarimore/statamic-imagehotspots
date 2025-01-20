<?php

namespace Darinlarimore\StatamicImagehotspots\GraphQL;

use Rebing\GraphQL\Support\Type as GQLType;
use GraphQL\Type\Definition\Type;

class HotImageType extends GQLType
{
    const NAME = "HotImageType";

    protected $attributes = [
        'name' => self::NAME,
    ];

    public function fields(): array
    {
        return [
            'url' => [
                'type' => Type::string(),
            ],
            'id' => [
                'type' => Type::string(),
            ],
            'fileName' => [
                'type' => Type::string(),
            ],
            'alt' => [
                'type' => Type::string(),
            ],
        ];
    }
}
