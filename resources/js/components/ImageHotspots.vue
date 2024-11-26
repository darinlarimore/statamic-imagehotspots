<template>
	<div>
		<assets-fieldtype
			v-if="!data.imageFile.url"
			class="assets-fieldtype"
			:value="imageFileId"
			ref="assets"
			handle="assets"
			:config="config"
			:readOnly="readOnly"
			@input="updateImageFile"
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
					class="i-absolute i-w-full i-h-full i-border-red-500 i-border-opacity-25 i-border-[48px]"
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
				<button @click="addHotspot" class="btn">Add Hotspot</button>
			</div>
			<div class="i-mt-2 i-w-full">
				<div class="i-grid i-gap-2">
					<div
						v-for="(hotspot, index) in data.hotspots"
						:key="index"
						class="flex gap-2 items-center"
					>
						<div class="replicator-set w-full">
							<div class="replicator-set-header cursor-pointer" @click="toggleOpen(index)">
								<span class="text-xs rtl:ml-2 ltr:mr-2 p-2 whitespace-nowrap">
									Hotspot {{ index }}
								</span>
								<div
									class="py-2 rtl:pr-2 ltr:pl-2 replicator-set-header-inner flex justify-end items-end w-full"
								>
									<button
										class="flex self-end group items-center"
										@click.stop="removeHotspot(index)"
										:aria-label="__('Delete Row')"
									>
										<svg-icon
											name="micro/trash"
											class="w-4 h-4 text-gray-600 group-hover:text-gray-900"
										/>
									</button>
								</div>
							</div>
							<div
                                class="replicator-set-body publish-fields @container"
                                v-show="isOpen(index)"
                            >
								<set-field
									v-for="field in fields"
                                    v-show="showField(field, index)"
									:key="field.handle"
									:field="field"
									:meta="meta.metas[index][field.handle]"
									:value="hotspot.content[field.handle]"
									:parent-name="name"
									:set-index="index"
									:errors="errors(field.handle, index)"
									:field-path="fieldPath(field.handle, index)"
									class="p-4"
									@updated="updated(field.handle, index, $event)"
									@meta-updated="metaUpdated(field.handle, index, $event)"
									@focus="$emit('focus')"
									@blur="$emit('blur')"
								/>
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
import Validator from '../../../vendor/statamic/cms/resources/js/components/field-conditions/Validator';
export default {
	mixins: [Fieldtype],

    inject: {
        storeName: {
            default: 'base'
        }
    },

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
				hotspots: this.value?.hotspots || [],
			},
            openHotspots: [],
		}
	},

	mounted() {
		this.config.max_files = 1
		this.config.min_files = 0
		this.config.mode = 'list'
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
								url: response.data[0].url,
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
            this.data.hotspots.push({ x: 50, y: 50, content: { ...JSON.parse(JSON.stringify(this.meta.defaults)) } })
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
				const hotSpotSize = 48
				const maxX = ((rect.width - hotSpotSize) / rect.width) * 100
				const maxY = ((rect.height - hotSpotSize) / rect.height) * 100
				const minX = 100 - maxX
				const minY = 100 - maxY

				const x = Math.min(
					maxX,
					Math.max(
						minX,
						startLeft + ((event.clientX - startX) / rect.width) * 100
					)
				)
				const y = Math.min(
					maxY,
					Math.max(
						minY,
						startTop + ((event.clientY - startY) / rect.height) * 100
					)
				)

				hotspot.x = x
				hotspot.y = y
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
			const state = this.$store.state.publish[this.storeName]
			if (!state) return []
			return state.errors[this.fieldPath(handle, index)] || []
		},

        showField(field, index) {
            let validator = new Validator(field, this.data.hotspots[index].content, this.$store, this.storeName);
            return validator.passesConditions();
        },
	},
}
</script>
