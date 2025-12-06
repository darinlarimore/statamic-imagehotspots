<?php

use Darinlarimore\StatamicImagehotspots\Tags\HotSpotImageTag;
use Statamic\Tags\Context;

beforeEach(function () {
    $this->tag = new HotSpotImageTag;
});

it('returns empty string when field parameter is not provided', function () {
    $this->tag->setContext(new Context);
    $this->tag->setParameters([]);

    $result = $this->tag->index();

    expect($result)->toBe('');
});

it('returns empty string when field is not found in context', function () {
    $this->tag->setContext(new Context(['other_field' => 'value']));
    $this->tag->setParameters(['field' => 'hotspots']);

    $result = $this->tag->index();

    expect($result)->toBe('');
});

it('handles page-level field objects with value method', function () {
    $fieldData = [
        'imageFile' => ['url' => '/images/test.jpg', 'id' => 'test-id'],
        'hotspots' => [
            ['x' => 10, 'y' => 20, 'content' => ['title' => 'Hotspot 1']],
            ['x' => 50, 'y' => 60, 'content' => ['title' => 'Hotspot 2']],
        ],
    ];

    $fieldObject = new class($fieldData)
    {
        private array $data;

        public function __construct(array $data)
        {
            $this->data = $data;
        }

        public function value(): array
        {
            return $this->data;
        }
    };

    $this->tag->setContext(new Context(['my_hotspots' => $fieldObject]));
    $this->tag->setParameters(['field' => 'my_hotspots']);

    $result = $this->tag->index();

    expect($result)->toBeArray()
        ->and($result['image'])->toBe($fieldData['imageFile'])
        ->and($result['hotspots'])->toBe($fieldData['hotspots'])
        ->and($result['hotspots'])->toHaveCount(2);
});

it('handles global field references with direct array data', function () {
    $fieldData = [
        'imageFile' => ['url' => '/images/global.jpg', 'id' => 'global-id'],
        'hotspots' => [
            ['x' => 30, 'y' => 40, 'content' => ['title' => 'Global Hotspot']],
        ],
    ];

    $this->tag->setContext(new Context(['global_hotspots' => $fieldData]));
    $this->tag->setParameters(['field' => 'global_hotspots']);

    $result = $this->tag->index();

    expect($result)->toBeArray()
        ->and($result['image'])->toBe($fieldData['imageFile'])
        ->and($result['hotspots'])->toBe($fieldData['hotspots'])
        ->and($result['hotspots'])->toHaveCount(1);
});

it('returns empty string when data is not an array', function () {
    $this->tag->setContext(new Context(['hotspots' => 'not-an-array']));
    $this->tag->setParameters(['field' => 'hotspots']);

    $result = $this->tag->index();

    expect($result)->toBe('');
});

it('returns empty string when data is null', function () {
    $this->tag->setContext(new Context(['hotspots' => null]));
    $this->tag->setParameters(['field' => 'hotspots']);

    $result = $this->tag->index();

    expect($result)->toBe('');
});

it('handles missing imageFile gracefully with null', function () {
    $fieldData = [
        'hotspots' => [
            ['x' => 10, 'y' => 20, 'content' => []],
        ],
    ];

    $this->tag->setContext(new Context(['hotspots' => $fieldData]));
    $this->tag->setParameters(['field' => 'hotspots']);

    $result = $this->tag->index();

    expect($result)->toBeArray()
        ->and($result['image'])->toBeNull()
        ->and($result['hotspots'])->toHaveCount(1);
});

it('handles missing hotspots gracefully with empty array', function () {
    $fieldData = [
        'imageFile' => ['url' => '/images/test.jpg'],
    ];

    $this->tag->setContext(new Context(['hotspots' => $fieldData]));
    $this->tag->setParameters(['field' => 'hotspots']);

    $result = $this->tag->index();

    expect($result)->toBeArray()
        ->and($result['image'])->toBe($fieldData['imageFile'])
        ->and($result['hotspots'])->toBe([]);
});

it('handles empty data array by returning empty string', function () {
    // Empty arrays are falsy and fail the !$data check
    $this->tag->setContext(new Context(['hotspots' => []]));
    $this->tag->setParameters(['field' => 'hotspots']);

    $result = $this->tag->index();

    expect($result)->toBe('');
});

it('handles field object that returns non-array value', function () {
    $fieldObject = new class
    {
        public function value(): string
        {
            return 'not-an-array';
        }
    };

    $this->tag->setContext(new Context(['hotspots' => $fieldObject]));
    $this->tag->setParameters(['field' => 'hotspots']);

    $result = $this->tag->index();

    expect($result)->toBe('');
});

it('handles field object that returns null', function () {
    $fieldObject = new class
    {
        public function value(): ?array
        {
            return null;
        }
    };

    $this->tag->setContext(new Context(['hotspots' => $fieldObject]));
    $this->tag->setParameters(['field' => 'hotspots']);

    $result = $this->tag->index();

    expect($result)->toBe('');
});

it('has correct static handle', function () {
    $reflection = new ReflectionClass(HotSpotImageTag::class);
    $property = $reflection->getProperty('handle');

    expect($property->getValue())->toBe('image_hot_spots');
});

it('preserves hotspot data structure', function () {
    $hotspots = [
        [
            'x' => 15.5,
            'y' => 25.5,
            'content' => [
                'title' => 'Test Title',
                'description' => 'Test Description',
                'link' => 'https://example.com',
            ],
        ],
    ];

    $fieldData = [
        'imageFile' => ['url' => '/images/test.jpg'],
        'hotspots' => $hotspots,
    ];

    $this->tag->setContext(new Context(['field' => $fieldData]));
    $this->tag->setParameters(['field' => 'field']);

    $result = $this->tag->index();

    expect($result['hotspots'][0]['x'])->toBe(15.5)
        ->and($result['hotspots'][0]['y'])->toBe(25.5)
        ->and($result['hotspots'][0]['content']['title'])->toBe('Test Title')
        ->and($result['hotspots'][0]['content']['description'])->toBe('Test Description');
});
