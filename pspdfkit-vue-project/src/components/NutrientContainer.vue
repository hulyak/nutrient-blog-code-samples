<!-- src/components/NutrientContainer.vue -->

<template>
    <div class="pdf-container"></div>
</template>

<script>
import NutrientViewer from '@nutrient-sdk/viewer';

export default {
    name: 'NutrientContainer',
    /**
     * The component receives the `pdfFile` prop, which is of type `String` and is required.
     */
    props: {
        pdfFile: {
            type: String,
            required: true,
        },
    },
    /**
     * We wait until the template has been rendered to load the document into the library.
     */
    mounted() {
        this.loadNutrient().then((instance) => {
            this.$emit('loaded', instance);
        });
    },
    /**
     * We watch for `pdfFile` prop changes and trigger unloading and loading when there's a new document to load.
     */
    watch: {
        pdfFile(val) {
            if (val) {
                this.loadNutrient();
            }
        },
    },
    /**
     * Our component has the `loadNutrient` method. This unloads and cleans up the component and triggers document loading.
     */
    methods: {
        async loadNutrient() {
            NutrientViewer.unload('.pdf-container');
            return NutrientViewer.load({
                // To access the `pdfFile` from props, use `this` keyword.
                document: this.pdfFile,
                container: '.pdf-container',
            });
        },
    },

    /**
     * Clean up when the component is unmounted so it's ready to load another document (not needed in this example).
     */
    beforeUnmount() {
        NutrientViewer.unload('.pdf-container');
    },
};
</script>

<style scoped>
.pdf-container {
    height: 100vh;
}
</style>
