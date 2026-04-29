<template>
	<div>
		<assets-fieldtype
			v-if="!data.imageFile.url"
			class="assets-fieldtype"
			:value="imageFileId"
			ref="assets"
			handle="assets"
			:config="assetsConfig"
			:meta="assetsMeta"
			:readOnly="readOnly"
			@update:value="updateImageFile"
		></assets-fieldtype>

		<div v-else>
			<div class="i-flex i-my-4 i-justify-between i-items-center i-p-4">
				<p class="i-text-sm">Selected image:</p>
				<div class="i-flex i-items-center i-gap-2">
					<p class="i-text-sm">{{ data.imageFile.fileName }}</p>
					<button @click="imageFileClear">×</button>
				</div>
			</div>
		</div>
		<div v-if="data.imageFile.error" class="d-text-red-500">
			{{ data.imageFile.error }}
		</div>

		<div v-if="data.imageFile.url">
			<div class="i-relative">
				<!-- hotspots should not be placed within the red border -->
				<div
					class="i-absolute i-top-0 i-bg-red-500 i-opacity-25"
					:style="{
						height: config.deadzone_top + '%',
						left: config.deadzone_left + '%',
						right: config.deadzone_right + '%',
					}"
				></div>
				<div
					class="i-absolute i-right-0 i-inset-y-0 i-bg-red-500 i-opacity-25"
					:style="{ width: config.deadzone_right + '%' }"
				></div>
				<div
					class="i-absolute i-bottom-0 i-bg-red-500 i-opacity-25"
					:style="{
						height: config.deadzone_bottom + '%',
						left: config.deadzone_left + '%',
						right: config.deadzone_right + '%',
					}"
				></div>
				<div
					class="i-absolute i-left-0 i-inset-y-0 i-bg-red-500 i-opacity-25"
					:style="{ width: config.deadzone_left + '%' }"
				></div>

				<img
					ref="image"
					:src="data.imageFile.url"
					class="i-w-full i-h-auto i-select-none"
				/>
				<div
					v-for="(hotspot, index) in data.hotspots"
					:key="index"
					class="i-absolute i-cursor-move -i-translate-x-[12px] -i-translate-y-[12px]"
					:style="{ top: hotspot.y + '%', left: hotspot.x + '%' }"
					@mousedown="dragStart(index, $event)"
				>
					<div
						class="i-w-6 i-h-6 i-bg-blue-500 i-border-white i-border-2 i-rounded-full i-flex i-justify-center i-items-center i-text-xs"
					>
						{{ index }}
					</div>
				</div>
			</div>
			<div class="i-mt-2">
				<button @click="addHotspot" style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; font-size: 13px; font-weight: 500; border: 1px solid #c4cdd6; border-radius: 8px; background: white; cursor: pointer; color: #1c2e36;">+ Add Hotspot</button>
			</div>
			<div class="i-mt-2 i-w-full">
				<div class="i-grid i-gap-2">
					<div
						v-for="(hotspot, index) in data.hotspots"
						:key="index"
						class="flex gap-2 items-center"
					>
						<div class="replicator-set w-full">
							<div
								class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-gray-700 cursor-pointer select-none"
								@click="toggleOpen(index)"
							>
								<div class="flex items-center gap-2">
									<svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-90': isOpen(index) }" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
									<span class="text-sm font-medium">
										Hotspot {{ index }}
									</span>
								</div>
								<button
									class="flex items-center group"
									@click.stop="removeHotspot(index)"
								>
									<svg class="w-4 h-4 text-gray-400 group-hover:text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
								</button>
							</div>
							<div
								v-show="isOpen(index)"
								style="width: 100%;"
							>
								<div
									v-for="field in fields"
									v-show="showField(field, index)"
									:key="field.handle"
									style="width: 100%; padding: 8px 16px; margin-bottom: 8px;"
								>
									<label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;" v-text="field.display || field.handle"></label>
									<div v-if="field.instructions" style="font-size: 12px; color: #737e8d; margin-bottom: 6px;">{{ field.instructions }}</div>
									<div style="width: 100%; min-width: 0;">
										<component
											:is="field.type + '-fieldtype'"
											:handle="field.handle"
											:config="field"
											:value="hotspot.content[field.handle]"
											:meta="meta.metas[index] ? meta.metas[index][field.handle] : {}"
											:read-only="readOnly"
											:field-path-prefix="fieldPath(field.handle, index)"
											@update:value="updated(field.handle, index, $event)"
											@update:meta="metaUpdated(field.handle, index, $event)"
											@focus="$emit('focus')"
											@blur="$emit('blur')"
										/>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
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

	data() {
		return {
			data: {
				imageFile: {
					url: this.value?.imageFile?.url || null,
					id: this.value?.imageFile?.id || null,
					fileName: this.value?.imageFile?.fileName || null,
					alt: this.value?.imageFile?.alt || null,
					error: this.value?.imageFile?.error || null,
				},
				hotspots: (this.value?.hotspots || []).map((h) => ({
					...h,
					content: h.content ?? {},
				})),
			},
			openHotspots: [],
		}
	},


	watch: {
		data: {
			deep: true,
			handler() {
				this.update(this.data)
			},
		},
	},
	computed: {
		imageFileId() {
			return this.data.imageFile.id ? [this.data.imageFile.id] : []
		},

		fields() {
			return this.config.fields
		},

		assetsMeta() {
			return {
				container: this.meta.container,
				data: null,
				dynamicFolder: null,
				rename_folder: null,
			}
		},

		assetsConfig() {
			return {
				container: this.config.container || this.meta.container,
				folder: this.config.folder,
				restrict: this.config.restrict,
				allow_uploads: this.config.allow_uploads,
				max_files: 1,
				min_files: 0,
				mode: 'list',
			}
		},
	},
	methods: {
		toggleOpen(index) {
			if (this.isOpen(index)) {
				this.openHotspots.splice(this.openHotspots.indexOf(index), 1)
			} else {
				this.openHotspots.push(index)
			}
		},

		isOpen(index) {
			return this.openHotspots.indexOf(index) >= 0
		},

		imageFileClear() {
			this.data.imageFile = {
				url: null,
				id: null,
				fileName: null,
				error: null,
			}
		},

		updateImageFile(assets) {
			const allowFileTypes = ['jpg', 'jpeg', 'png', 'gif', 'svg']

			if (assets[0]) {
				this.$axios
					.post(this.cpUrl('assets-fieldtype'), {
						assets: [assets[0]],
					})
					.then((response) => {
						if (allowFileTypes.includes(response.data[0].extension)) {
							this.data.imageFile = {
								url: response.data[0].url ?? response.data[0].downloadUrl,
								id: response.data[0].id,
								fileName: response.data[0].basename,
								alt: response.data[0].values.alt,
							}
						} else {
							this.data.imageFile = {
								error: 'Invalid file type',
							}
						}
					})
			}
		},

		addHotspot() {
			this.data.hotspots.push({
				x: 50,
				y: 50,
				content: { ...JSON.parse(JSON.stringify(this.meta.defaults)) },
			})
			this.meta.metas.push(JSON.parse(JSON.stringify(this.meta.defaultmeta)))
			this.toggleOpen(this.data.hotspots.length - 1)
		},

		removeHotspot(index) {
			this.data.hotspots.splice(index, 1)
			this.meta.metas.splice(index, 1)
		},

		dragStart(index, event) {
			const hotspot = this.data.hotspots[index]
			const startX = event.clientX
			const startY = event.clientY
			const startLeft = hotspot.x
			const startTop = hotspot.y

			const mouseMove = (event) => {
				// Constrain to image bounds and account for size of the hotspot
				const rect = this.$refs.image.getBoundingClientRect()
				const maxX = 100 - this.config.deadzone_right
				const maxY = 100 - this.config.deadzone_bottom
				const minX = this.config.deadzone_left
				const minY = this.config.deadzone_top
				const mouseY = startTop + ((event.clientY - startY) / rect.height) * 100
				const mouseX = startLeft + ((event.clientX - startX) / rect.width) * 100

				hotspot.x = Math.min(Math.max(mouseX, minX), maxX)
				hotspot.y = Math.min(Math.max(mouseY, minY), maxY)
			}

			const mouseUp = () => {
				document.removeEventListener('mousemove', mouseMove)
				document.removeEventListener('mouseup', mouseUp)
			}

			document.addEventListener('mousemove', mouseMove)
			document.addEventListener('mouseup', mouseUp)
		},

		cpUrl(url) {
			url = Statamic.$config.get('cpUrl') + '/' + url
			return url.replace(/([^:])(\/\/+)/g, '$1/')
		},

		updated(handle, index, value) {
			this.data.hotspots[index].content[handle] = value
		},

		metaUpdated(handle, index, value) {
			this.meta.metas[index][handle] = value
			this.$emit('meta-updated', this.meta)
		},

		fieldPath(handle, index) {
			return `${this.fieldPathPrefix}.hotspots.${index}.content.${handle}`
		},

		errors(handle, index) {
			return []
		},

		showField(field, index) {
			if (!field.if && !field.if_any && !field.show_when && !field.show_when_any && !field.unless && !field.unless_any && !field.hide_when && !field.hide_when_any) {
				return true
			}

			const conditions = field.if || field.if_any || field.show_when || field.show_when_any || field.unless || field.unless_any || field.hide_when || field.hide_when_any
			const values = this.data.hotspots[index].content
			const isNegative = !!(field.unless || field.unless_any || field.hide_when || field.hide_when_any)
			const matchAll = !!(field.if || field.show_when || field.unless || field.hide_when)

			const results = Object.entries(conditions).map(([key, expected]) => {
				const actual = values[key]
				if (expected === 'empty') return !actual || actual === '' || (Array.isArray(actual) && actual.length === 0)
				if (expected === 'not empty') return !!actual && actual !== '' && !(Array.isArray(actual) && actual.length === 0)
				if (typeof expected === 'string' && expected.startsWith('not ')) return String(actual) !== expected.slice(4)
				return String(actual) === String(expected)
			})

			const passes = matchAll ? results.every(Boolean) : results.some(Boolean)
			return isNegative ? !passes : passes
		},
	},
}
</script>
