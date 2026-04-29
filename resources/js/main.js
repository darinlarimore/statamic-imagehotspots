import '../css/main.css'
import Fieldtype from './components/ImageHotspots.vue'

Statamic.booting(() => {
	Statamic.$components.register('image_hot_spots-fieldtype', Fieldtype)
})
