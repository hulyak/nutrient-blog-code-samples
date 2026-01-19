// src/components/PSPDFKitContainer.vue

<template>
	<div class="doc-container"></div>
</template>

<script>
import NutrientViewer from '@nutrient-sdk/viewer';

export default {
	name: 'PSPDFKit',
	/**
	 * The component receives the `excelFile` prop, which is of type `String` and is required.
	 */
	props: {
		excelFile: {
			type: String,
			required: true,
		},
	},
	/**
	 * We wait until the template has been rendered to load the document into the library.
	 */
	mounted() {
		this.loadPSPDFKit().then((instance) => {
			this.$emit('loaded', instance);
		});
	},
	/**
	 * We watch for `excelFile` prop changes and trigger unloading and loading when there's a new document to load.
	 */
	watch: {
		excelFile(val) {
			if (val) {
				this.loadPSPDFKit();
			}
		},
	},
	/**
	 * Our component has the `loadPSPDFKit` method. This unloads and cleans up the component and triggers document loading.
	 */
	methods: {
		async loadPSPDFKit() {
			NutrientViewer.unload('.doc-container');
			return NutrientViewer.load({
				// To access the `excelFile` from props, use `this` keyword.
				document: this.excelFile,
				container: '.doc-container',
			});
		},
	},

	/**
	 * Clean up when the component is unmounted so it's ready to load another document (not needed in this example).
	 */
	beforeUnmount() {
		NutrientViewer.unload('.doc-container');
	},
};
</script>

<style scoped>
.doc-container {
	height: 100vh;
}
</style>
