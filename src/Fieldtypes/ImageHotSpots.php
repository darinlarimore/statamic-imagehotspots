<?php

namespace Darinlarimore\StatamicImagehotspots\Fieldtypes;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Statamic\Fields\Fieldtype;
use Statamic\Exceptions\AssetContainerNotFoundException;
use Statamic\Facades\AssetContainer;
use Statamic\Fields\Fields;
use Statamic\Fields\Values;
use Statamic\Fieldtypes\Assets\UndefinedContainerException;

class ImageHotSpots extends Fieldtype
{
    protected function defaultRowData()
    {
        return $this->fields()->all()->map(function ($field) {
            return $field->fieldtype()->preProcess($field->defaultValue());
        });
    }

    public function process($data)
    {
        return collect($data)->map($this->processRow(...))->all();
    }

    private function processRow($row, $index)
    {
        if ($index === 'hotspots' || $index === 'content') {
            return collect($row)->map($this->processRow(...))->all();
        }

        return array_merge($row, $this->fields($index)->addValues($row)->process()->values()->all());
    }

    public function preProcess($data)
    {
        return collect($data)->map($this->preProcessRow(...))->all();
    }

    private function preProcessRow($row, $index)
    {
        if ($index === 'hotspots' || $index === 'content') {
            return collect($row)->map($this->preProcessRow(...))->all();
        }

        return array_merge($row, $this->fields($index)->addValues($row)->preProcess()->values()->all());
    }

    public function preload()
    {
        return [
            'defaults' => $this->defaultRowData()->all(),
            'data' => $this->getItemData($this->field->value() ?? []),
            'metas' => $this->fields()->meta()->all(),
        ];
    }

    public function getItemData($items)
    {
        return $items;
    }

    public function augment($value)
    {
        return $this->performAugmentation($value, false);
    }

    public function shallowAugment($value)
    {
        return $this->performAugmentation($value, true);
    }

    private function performAugmentation($value, $shallow)
    {
        return collect($value)->map(function ($row, $index) use ($shallow) {
            return $this->augmentRow($index, $row, $shallow);
        })->all();
    }

    private function augmentRow($index, $row, $shallow)
    {
        if ($index === 'hotspots') {
            return collect($row)->map(function ($row, $index) use ($shallow) {
                return $this->augmentRow($index, $row, $shallow);
            })->all();
        }

        if (array_key_exists('content', $row)) {
            return new Values(
                array_merge($row, [
                    'content' => $this->augmentRow('content', $row['content'], $shallow)
                ])
            );
        }

        $method = $shallow ? 'shallowAugment' : 'augment';

        if ($index == 'imageFile') {
            return $row;
        }

        // Fix assets breaking when they start with `asset::`
        foreach ($this->fieldsByType('assets') as $field) {
            $assets = Arr::wrap($row[$field] ?? []);
            $row[$field] = Arr::map($assets, fn($asset) => Str::after($asset, '::'));
        }

        $data = $this->fields($index)->addValues($row)->{$method}()->values();

        return new Values($data->filter(fn($value) => $value->raw())->all());
    }

    public function fields($index = -1)
    {
        $fields = $this->config('fields');

        return new Fields($fields, $this->field()->parent(), $this->field(), $index);
    }

    public function fieldsByType($type)
    {
        return collect($this->config('fields'))
            ->filter(fn($field) => ($field['field']['type'] ?? null) === $type)
            ->pluck('handle');
    }

    protected $icon = 'add-circle';

    public $categories = ['media'];

    protected function container()
    {
        if ($configured = $this->config('container')) {
            if ($container = AssetContainer::find($configured)) {
                return $container;
            }

            throw new AssetContainerNotFoundException($configured);
        }

        if (($containers = AssetContainer::all())->count() === 1) {
            return $containers->first();
        }

        throw new UndefinedContainerException;
    }

    protected function configFieldItems(): array
    {
        return [
            [
                'display' => __('Fields'),
                'fields' => [
                    'fields' => [
                        'display' => __('Fields'),
                        'instructions' => __('statamic::fieldtypes.grid.config.fields'),
                        'type' => 'fields',
                        'full_width_setting' => true,
                    ],
                ],
            ],
            [
                'display' => __('Asset'),
                'fields' => [
                    'container' => [
                        'display' => __('Container'),
                        'instructions' => __('statamic::fieldtypes.assets.config.container'),
                        'type' => 'asset_container',
                        'max_items' => 1,
                        'mode' => 'select',
                        'width' => 50,
                    ],
                    'folder' => [
                        'display' => __('Folder'),
                        'instructions' => __('statamic::fieldtypes.assets.config.folder'),
                        'type' => 'asset_folder',
                        'max_items' => 1,
                        'width' => 50,
                    ],
                    'restrict' => [
                        'display' => __('Restrict'),
                        'instructions' => __('statamic::fieldtypes.assets.config.restrict'),
                        'type' => 'toggle',
                        'width' => 50,
                    ],
                    'allow_uploads' => [
                        'display' => __('Allow Uploads'),
                        'instructions' => __('statamic::fieldtypes.assets.config.allow_uploads'),
                        'type' => 'toggle',
                        'default' => true,
                        'width' => 50,
                    ],
                ]
            ],
        ];
    }
}
