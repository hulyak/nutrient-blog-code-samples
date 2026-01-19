// index.js
import './assets/nutrient-viewer.js';

// Set the path to where the viewer assets (like fonts and wasm) are located.
const baseUrl = `${window.location.protocol}//${window.location.host}/assets/`;

$(document).ready(function () {
    NutrientViewer.load({
        baseUrl, // Points to the folder containing 'nutrient-viewer-lib'.
        container: '#nutrient', // The DOM element where the viewer will be mounted.
        document: 'chart.xlsx', // The file to render. This can be a PDF, XLS, or XLSX file.
        
    })
        .then((instance) => {
            console.log('Nutrient loaded', instance);
        })
        .catch((error) => {
            console.error(error.message);
        });
});
