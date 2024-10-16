<template>
	<div>
		<assets-fieldtype
			class="assets-fieldtype"
			:value="imageFileId"
			ref="assets"
			handle="assets"
			:config="config"
			:readOnly="readOnly"
			@input="updateImageFile"
		></assets-fieldtype>

		<div v-if="data.imageFile.url">
			<div class="i-relative">
				<!-- hotspots should not be placed within the red border -->
				<div class="i-absolute i-w-full i-h-full i-border-red-500 i-border-opacity-25 i-border-[48px]"></div>
				<img ref="image" :src="data.imageFile.url" class="i-w-full i-h-auto i-select-none" />
				<div
					v-for="(hotspot, index) in data.hotspots"
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
					<div v-for="(hotspot, index) in data.hotspots" :key="index" class="flex gap-2 items-center">
						<div title="x: {hotspot.y}" class="i-w-6 i-h-6 i-flex-none i-bg-blue-500 i-border-white i-border-2 i-rounded-full i-flex i-justify-center i-items-center i-text-xs">{{ index }}</div>
						<textarea rows="2" type="text" class="input-text" v-model="hotspot.content" />
						<div>
							<button @click="data.hotspots.splice(index, 1)"> × </button>
						</div>
					</div>
				</div>
			</div>
			<div class="i-mt-2">
				<button @click="addHotspot" class="btn">Add Hotspot</button>
			</div>
		</div>

		<div v-else class="i-flex i-justify-between i-items-center">
			<p class="i-mt-2 i-text-sm">Select an image</p>
		</div>
	</div>
</template>

<script>
	export default {
		mixins: [Fieldtype],
		inject: ['storeName'],
		data() {
				return {
					data: {
						imageFile: {
							url: this.value?.imageFile?.url || null,
							id: this.value?.imageFile?.id || null,
							fileName: this.value?.imageFile?.fileName || null,
							error: this.value?.imageFile?.error || null,
						},
						hotspots: this.value?.hotspots || [],
					}
				};
		},

		mounted() {
			this.config.max_files = 1;
			this.config.min_files = 0;
			this.config.mode = 'list';
		},

		watch: {
			data: {
				deep: true,
				handler() {
					this.update(this.data);
				}
			}
		},
		computed: {
			imageFileId() {
				return this.data.imageFile.id ? [this.data.imageFile.id] : [];
			},
		},
		methods: {

			updateImageFile(assets) {
				const allowFileTypes = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

				if (assets[0]) {
					this.$axios
					.post(this.cpUrl("assets-fieldtype"), {
							assets:[ assets[0] ],
					})
					.then((response) => {
						if (allowFileTypes.includes(response.data[0].extension)) {
							this.data.imageFile = {
								url: response.data[0].url,
								id: response.data[0].id,
								fileName: response.data[0].basename,
								alt: response.data[0].values.alt,
							};
						} else {
							this.data.imageFile = {
								error: "Invalid file type",
							};
						}
					})
				}
			},

			addHotspot() {
				this.data.hotspots.push({ x: 50, y: 50, content: '' });
			},

			dragStart(index, event) {
				const hotspot = this.data.hotspots[index];
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
				};

				const mouseUp = () => {
					document.removeEventListener('mousemove', mouseMove);
					document.removeEventListener('mouseup', mouseUp);
				};

				document.addEventListener('mousemove', mouseMove);
				document.addEventListener('mouseup', mouseUp);
			},

			cpUrl(url) {
				url = Statamic.$config.get("cpUrl") + "/" + url;
				return url.replace(/([^:])(\/\/+)/g, "$1/");
			},
		}
	};
</script>
