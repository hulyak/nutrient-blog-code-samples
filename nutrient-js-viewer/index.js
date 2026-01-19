import "./assets/nutrient-viewer.js";

const baseUrl = `${window.location.protocol}//${window.location.host}/assets/`;

NutrientViewer.load({
  baseUrl,
  container: "#nutrient",
  document: "document.pdf",
})
  .then((instance) => {
    console.log("Nutrient loaded", instance);
  })
  .catch((error) => {
    console.error(error.message);
  });
