import "./assets/nutrient-viewer.js";

const baseUrl = `${window.location.protocol}//${window.location.host}/assets/`;

NutrientViewer.load({
  baseUrl,
  container: "#nutrient",
  document: "document.docx",
})
  .then((instance) => {
    // Convert to PDF
    return instance.exportPDF();
  })
  .then((buffer) => {
    // Download the PDF
    const blob = new Blob([buffer], { type: "application/pdf" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = "output.pdf";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    URL.revokeObjectURL(url);
  })
  .catch((error) => {
    console.error("Error:", error.message);
  });
