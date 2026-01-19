// src/App.vue

<template>
	<div id="app">
		<label for="file-upload" class="custom-file-upload">
			Open Excel
		</label>
		<input
			id="file-upload"
			type="file"
			@change="openDocument"
			class="btn"
		/>
		<PSPDFKitContainer
			:excelFile="excelFile"
			@loaded="handleLoaded"
		/>
	</div>
</template>

<script>
import PSPDFKitContainer from '@/components/PSPDFKitContainer';

export default {
	data() {
		return {
			excelFile: this.excelFile || '/chart.xlsx',
		};
	},
	/**
	 * Render the `PSPDFKitContainer` component.
	 */
	components: {
		PSPDFKitContainer,
	},
	/**
	 * Our component has two methods — one to check when the document is loaded, and the other to open the document.
	 */
	methods: {
		handleLoaded(instance) {
			console.log('PSPDFKit has loaded: ', instance);
			// Do something.
		},

		openDocument() {
			// To access the Vue instance data properties, use `this` keyword.
			if (this.excelFile) {
				window.URL.revokeObjectURL(this.excelFile);
			}
			this.excelFile = window.URL.createObjectURL(
				event.target.files[0],
			);
		},
	},
};
</script>

<style>
#app {
	text-align: center;
	color: #2c3e50;
}

body {
	margin: 0;
}

input[type='file'] {
	display: none;
}

.custom-file-upload {
	border: 1px solid #ccc;
	border-radius: 4px;
	display: inline-block;
	padding: 6px 12px;
	cursor: pointer;
	background: #4a8fed;
	padding: 10px;
	color: #fff;
	font: inherit;
	font-size: 16px;
	font-weight: bold;
}
</style>
