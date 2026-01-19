const { load } = require("@nutrient-sdk/node");
const fs = require("fs");

(async () => {
  const jpgImage = fs.readFileSync("input.jpg");

  const instance = await load({ document: jpgImage });
  const buffer = await instance.exportPDF();

  fs.writeFileSync("converted-jpg.pdf", Buffer.from(buffer));
  instance.close();
  console.log("Nutrient SDK: JPEG converted successfully!");
})();
