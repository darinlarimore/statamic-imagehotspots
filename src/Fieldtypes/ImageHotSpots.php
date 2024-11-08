<?php

namespace Darinlarimore\StatamicImagehotspots\Fieldtypes;

use Statamic\Fields\Fieldtype;
use Statamic\Exceptions\AssetContainerNotFoundException;
use Statamic\Facades\AssetContainer;
use Statamic\Fields\Fields;
use Statamic\Fieldtypes\Assets\UndefinedContainerException;
use Statamic\Statamic;

class ImageHotSpots extends Fieldtype
{
    protected function defaultRowData()
    {
        return $this->fields()->all()->map(function ($field) {
            return $field->fieldtype()->preProcess($field->defaultValue());
        });
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
        $version = Statamic::version();
        $versionArray = explode('.', $version);
        return [
            'defaults' => $this->defaultRowData()->all(),
            'data' => $this->getItemData($this->field->value() ?? []),
            'metas' => $this->fields()->meta()->all(),
            'container' => $this->container()->handle(),
            'statamic_version' => $version,
            'statamic_major_version' => isset($versionArray[0]) ? (int)$versionArray[0] : 4
        ];
    }

    public function getItemData($items)
    {
        return $items;
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

    public function fields($index = -1)
    {
        return new Fields($this->config('fields'), $this->field()->parent(), $this->field(), $index);
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
                'display' => __('Assets'),
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
