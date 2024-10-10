# <img src="src/icon.svg" height="20" width="20"> Statamic Imagehotspots

> Statamic Imagehotspots is a Statamic addon that allows you to add hotspots to images.

## Features
This addon provides the following features:
- Save hotspot positions in percentages for absolute positioning usage
- Accompanying hotspot description text field
- Supports SVG images

## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require darinlarimore/statamic-imagehotspots
```

## How to Use

**Note:** This `Image Hot Spots` field is intended to be used in conjunction with an `Assets` field. Configure the `Image Field Handle` setting to match this the `Assets` field handle.

### Setup Fields
1. Create an `Assets` field for Image Hot Spots to pull from. Configure the Max Files setting to 1, it will only pull in the first image.
2. Add an `Image Hot Spots` field and configure the required `Image Field Handle` setting to match the previous `Assets` field's handle.

![Image Hot Spots Field](/fieldType.png)

### Usage
1. Add an image to the `Assets` field.
2. Click the `Refresh Image` button.
3. Click the `Add Hotspot` button.
4. Click and drag the hotspot to the desired position.
5. Add a description for the hotspot.
6. When selecting a new image click `Refresh Image` to load the new image into the field.

![Image Hot Spots Field](/fields.png)
**Note:** Hotspots cannot be placed in the red border area to prevent breaking the page bounds at certain sizes.

### Front End Templating Example
This example uses Tailwind, Alpine.js, and the X-anchor alpine.js plugin.

```html
<section>
	<div class="relative">
		<img src="{{glide:hot_spot_image w=1280}}" alt="">

		{{ image_hot_spots.hotspots }}
			<div
				x-data="{ open: false }"
				class="absolute -translate-x-[12px] -translate-y-[12px]"
				style="top: {{ y }}%; left: {{ x }}%;"
				@mouseOver="open = true"
				@mouseLeave="open = false"
			>
				<div
					class="w-6 h-6 bg-blue-500 border-white border-2 rounded-full
						flex justify-center items-center text-xs text-white font-bold cursor-pointer"
					x-ref="button"
				>
					{{svg src="heroicons/solid/plus.svg" class="size-6"}}
				</div>

				{{# tooltip #}}
				<div
					class="bg-black p-4 shadow-lg w-64 text-white z-10"
					x-cloak
					x-show="open"
					x-anchor.offset.5="$refs.button"
				>
					{{ content }}
				</div>
			</div>
		{{ /image_hot_spots.hotspots }}
		</div>
	</div>

</section>
```
**Note:** the `-translate-x` and `-translate-y` classes are used to center the hotspots accurately and helps prevent the hotspots from breaking the page bounds.

![Image Hot Spots Front End Example](/imageHotspots.png)
