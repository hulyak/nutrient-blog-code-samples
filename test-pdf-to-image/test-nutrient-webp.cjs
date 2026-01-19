const { load } = require("@nutrient-sdk/node");
const fs = require("fs");

(async () => {
  const doc = fs.readFileSync("document.pdf");

  const instance = await load({ document: doc });
  const pageWidth = instance.getDocumentInfo().pages[0].width;

  // Export as WebP instead of PNG
  const result = await instance.renderPage(0, { width: pageWidth }, "webp");

  fs.writeFileSync("image.webp", Buffer.from(result));
  instance.close();
  console.log("Nutrient SDK: PDF converted to WebP successfully!");
})();
