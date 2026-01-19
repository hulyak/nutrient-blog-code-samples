const { load } = require("@nutrient-sdk/node");
const fs = require("fs");

(async () => {
  const doc = fs.readFileSync("document.pdf");

  const instance = await load({ document: doc });
  const pageWidth = instance.getDocumentInfo().pages[0].width;
  const result = await instance.renderPage(0, { width: pageWidth });

  fs.writeFileSync("image.png", Buffer.from(result));
  instance.close();
  console.log("Nutrient SDK: PDF converted to image successfully!");
  console.log("Output: image.png");
})();
