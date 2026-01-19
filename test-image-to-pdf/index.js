const { load } = require("@nutrient-sdk/node");
const fs = require("fs");

(async () => {
  const pngImage = fs.readFileSync("input.png");

  const instance = await load({ document: pngImage });
  const buffer = await instance.exportPDF();

  fs.writeFileSync("converted.pdf", Buffer.from(buffer));
  instance.close();
  console.log("Nutrient SDK: Image converted successfully!");
})();
