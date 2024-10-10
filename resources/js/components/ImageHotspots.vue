<template>
	<div v-if="this.meta.image">
		<div class="i-my-2 i-flex i-justify-end">
			<button @click="refreshImage" class="btn">Refresh Image</button>
		</div>
		<div class="i-relative">
			<!-- hotspots should not be placed within the red border -->
			<div class="i-absolute i-w-full i-h-full i-border-red-500 i-border-opacity-25 i-border-[48px]"></div>
			<img ref="image" :src="this.meta.image" class="i-w-full i-h-auto i-select-none" />
			<div
				v-for="(hotspot, index) in hotspots"
				:key="index"
				class="i-absolute i-cursor-move -i-translate-x-[12px] -i-translate-y-[12px]"
				:style="{ top: hotspot.y + '%', left: hotspot.x + '%' }"
				@mousedown="dragStart(index, $event)"
			>
				<div class="i-w-6 i-h-6 i-bg-blue-500 i-border-white i-border-2 i-rounded-full i-flex i-justify-center i-items-center i-text-xs">{{ index }}</div>
			</div>
		</div>
		<div class="i-mt-2 i-w-full">
			<div class="i-grid i-gap-2">
				<div v-for="(hotspot, index) in hotspots" :key="index" class="flex gap-2 items-center">
					<div title="x: {hotspot.y}" class="i-w-6 i-h-6 i-flex-none i-bg-blue-500 i-border-white i-border-2 i-rounded-full i-flex i-justify-center i-items-center i-text-xs">{{ index }}</div>
					<textarea rows="2" type="text" class="input-text" v-model="hotspot.content" @change="updateValue" />
					<div>
						<button @click="hotspots.splice(index, 1)"> × </button>
					</div>
				</div>
			</div>
		</div>
		<div class="i-mt-2">
			<button @click="addHotspot" class="btn">Add Hotspot</button>
		</div>
	</div>

	<div v-else class="i-flex i-justify-between i-items-center">
		<p class="i-mt-2 i-text-sm">Select an image and click the refresh button</p>
		<div class="i-mt-2">
			<button @click="refreshImage" class="btn">Refresh Image</button>
		</div>
	</div>
</template>

<script>
	export default {
		mixins: [Fieldtype],
		inject: ['storeName'],
		data() {
				return {
						hotspots: this.value?.hotspots || [],
				};
		},

		mounted() {
			if (!this.meta.image) {
				this.refreshImage()
			}
		},
		computed: {
		},
		methods: {
			refreshImage() {
				const values = this.$store.state.publish[this.storeName].values;
				const imageField = this.config.imageFieldHandle;

				// Search recursively in object for the imageField
				const findImage = (obj) => {
					for (const key in obj) {
						if (key === imageField) {
							return obj[key];
						}
						if (typeof obj[key] === 'object') {
							const result = findImage(obj[key]);
							if (result) {
								return result;
							}
						}
					}
				};

				// Send image to the asset thumbnail controller
				this.$axios.post('/!/statamic-imagehotspots/asset' ,
					findImage(values)[0]
				).then((response) => {
					this.updateMeta({image: response.data});
				});
			},
			addHotspot() {
				this.hotspots.push({ x: 50, y: 50 });
				this.updateValue();
			},
			updateValue() {
				this.$emit('input', { hotspots: this.hotspots });
			},
			dragStart(index, event) {
				const hotspot = this.hotspots[index];
				const startX = event.clientX;
				const startY = event.clientY;
				const startLeft = hotspot.x;
				const startTop = hotspot.y;

				const mouseMove = (event) => {
					// Constrain to image bounds and account for size of the hotspot
					const rect = this.$refs.image.getBoundingClientRect();
					const hotSpotSize = 48;
					const maxX = (rect.width - hotSpotSize) / rect.width * 100;
					const maxY = (rect.height - hotSpotSize) / rect.height * 100;
					const minX = 100 - maxX;
					const minY = 100 - maxY;

					const x = Math.min(maxX, Math.max(minX, startLeft + ((event.clientX - startX) / rect.width) * 100));
					const y = Math.min(maxY, Math.max(minY, startTop + ((event.clientY - startY) / rect.height) * 100));

					hotspot.x = x;
					hotspot.y = y;

					this.updateValue();
				};

				const mouseUp = () => {
					document.removeEventListener('mousemove', mouseMove);
					document.removeEventListener('mouseup', mouseUp);
				};

				document.addEventListener('mousemove', mouseMove);
				document.addEventListener('mouseup', mouseUp);
			},
		}
	};
</script>
