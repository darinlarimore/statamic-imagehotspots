# <img src="src/icon.svg" height="20" width="20"> Statamic Imagehotspots

> Statamic Imagehotspots is a Statamic addon that allows you to add hotspots to images.

## Features
This addon provides the following features:
- Save hotspot positions in percentages for absolute positioning usage
- Supports SVG images
- Antlers tag to access hotspots in your templates

## How to Install
You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require darinlarimore/statamic-imagehotspots
```

## How to Use

### Setup Fields
Add an `Image Hot Spots` field and configure the required asset `Container` setting

![Image Hot Spots Field](/fieldType.png)

### Usage
1. Add an image to the `Assets` field.
2. Click the `Refresh Image` button.
3. Click the `Add Hotspot` button.
4. Click and drag the hotspot to the desired position.
5. Add a description for the hotspot.

![Image Hot Spots Field](/fields.png)
**Note:** Red border area prevents breaking the page bounds at certain sizes. This is is configurable in the fieldset.

### Front End Templating Example
This example uses Tailwind, Alpine.js, and the X-anchor alpine.js plugin. The `{{ image_hot_spots }}{{ /image_hot_spots }}` tag pair is used to loop through the hotspots and the `{{ hotspots }}{{ /hotspots }}` tag pair is used to access the hotspot data.

```html
<section>
	{{ image_hot_spots field="image_hot_spots_field" }}
		<div class="relative">
			<img src="{{glide :src="image.url" w=1280}}" class="w-full" alt="{{image.alt}}">

			{{ hotspots }}
				<div
					x-data="{ open: false }"
					class="absolute"
					style="top: {{y}}%; left: {{x}}%; transform: translate(-12px, -12px);"
					@mouseOver="open = true"
					@mouseLeave="open = false"
				>
					<div
						class="w-8 h-8 bg-blue-500 border-white border-2 rounded-full
							flex justify-center items-center text-xs text-white font-bold cursor-pointer"
						x-ref="button"
					>
						{{svg src="heroicons/solid/plus.svg" class="size-6"}}
					</div>

					{{# tooltip #}}
					<div
						class="bg-white p-4 shadow-lg w-96 z-10 flex flex-col gap-4 items-start"
						x-cloak
						x-show="open"
						x-anchor.offset.5="$refs.button"
					>
						<h3 class="h3">{{ content.heading }}</h3>
						{{ img :src="content.image" size="400xAUTO" class="" }}
						{{ content.text }}
						{{ link:content.callout class="button" }}
					</div>
				</div>
			{{ /hotspots }}
		</div>
	{{ /image_hot_spots }}
</section>
```

**Note:** the `-translate-x` and `-translate-y` classes are used to center the hotspots accurately and helps prevent the hotspots from breaking the page bounds.

![Image Hot Spots Front End Example](/imageHotspots.png)

### v2.0.0
**Note:** Breaking changes from v1 to v2:
- The content of each hotspot now accepts an array of fieldtypes configured in the fieldset. Yay! But this will require you to reconfigure the fieldset, update your templates, and re-add content to your existing hotspots fields.

### Contributors:
@Jade-GG - Added support support for arbitrary fieldtypes in the hotspot content.
