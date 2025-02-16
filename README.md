# <img src="src/icon.svg" height="20" width="20"> Statamic Imagehotspots

> Statamic Imagehotspots is a Statamic addon that allows you to add hotspots to images.

## Features
This addon provides the following features:
- Save hotspot positions in percentages for absolute positioning usage
- Supports SVG images
- Antlers tag to access hotspots in your templates
- Assign fields for hotspot content

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
2. Click the `Add Hotspot` button.
3. Click and drag the hotspot to the desired position.
4. Add content to the hotspot.

![Image Hot Spots Field](/fields.png)
**Note:** Red border area prevents breaking the page bounds at certain sizes. This is is configurable in the fieldset.

### Front End Templating Example
This example uses Tailwind, Alpine.js, and the X-anchor alpine.js plugin. The `{{ image_hot_spots }}{{ /image_hot_spots }}` tag pair is used to loop through the hotspots and the `{{ hotspots }}{{ /hotspots }}` tag pair is used to access the hotspot data.

```html
<section>
	{{ image_hot_spots field="image_hot_spots_field" }}
		<div class="relative z-20">
			<img src="{{glide :src="image.url" w=1280}}" class="w-full rounded-lg" alt="{{image.alt}}">

			{{ hotspots }}
			<div x-data="{ open: false }">
				<div
					class="absolute"
					:class="{ 'z-50': open }"
					style="top: {{y}}%; left: {{x}}%; transform: translate(-12px, -12px);"
					@mouseOver="open = true"
					@mouseLeave="open = false"
				>
					<div
						class="w-8 h-8 bg-blue-500 border-white border-2 rounded-full
							flex justify-center items-center text-white font-bold cursor-pointer"
						x-ref="button"
					>
						<svg class="size-4" width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M50.9 33.1113H33.0955V50.901C33.0955 53.7169 30.8235 56 28.0076 56C25.1917 56 22.921 53.7169 22.921 50.8998V33.099H5.10782C2.29191 33.099 0.00267483 30.8159 0.00390825 27.9988C0.00267483 26.5914 0.571284 25.3013 1.49265 24.3799C2.41526 23.4561 3.68815 22.8726 5.09426 22.8726H22.921V5.09651C22.921 3.68794 23.4797 2.41258 24.4023 1.49244C25.3249 0.56984 26.5941 -0.00123397 28.0027 -0.00123397C30.8174 -0.00123397 33.0955 2.28184 33.0955 5.09651V22.8739H50.9C53.7159 22.8739 55.999 25.1767 55.9977 27.9926C55.9965 30.8073 53.7134 33.1113 50.9 33.1113Z" fill="white"/> </svg>
					</div>

					{{# tooltip #}}
					<div
						x-show="open"
						x-cloak
						x-transition
						x-anchor="$refs.button"
						class="p-4"
					>
						<div
							class="bg-white/65 p-4 shadow-lg w-96 flex-col gap-4 items-start flex rounded-lg backdrop-blur"
						>
							<h3 class="h3">{{ content.heading }}</h3>
							{{ if content.text }}
								<p class="line-text-sm">
									{{ content.text | truncate(256, '...') }}
								</p>
							{{ /if }}
						</div>
					</div>
				</div>

			</div>
			{{ /hotspots }}
		</div>
	{{ /image_hot_spots }}
</section>
```

**Note:** the `-translate-x` and `-translate-y` classes are used to center the hotspots accurately and helps prevent the hotspots from breaking the page bounds.

![Image Hot Spots Front End Example](/imageHotspots.png)

### GraphQL Support
This addon supports GraphQL. You can access the hotspots data in your GraphQL queries like so:

```graphql
{
	imageHotspots {
		imageFile {
			url
			id
			fileName
			alt
		}
		hotspots {
			x
			y
			content
		}
	}
}
```

### v2.0.0
**Note:** Breaking changes from v1 to v2:
- The content of each hotspot now accepts an array of fieldtypes configured in the fieldset. Yay! But this will require you to reconfigure the fieldset, update your templates, and re-add content to your existing hotspots fields.

### Contributors:
- @Jade-GG - Added support support for arbitrary fieldtypes in the hotspot content.
- @emran-alhaddad - Added GraphQL support.
