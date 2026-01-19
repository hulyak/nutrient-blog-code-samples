const fs = require("fs");
const { PDFDocument } = require("pdf-lib");

async function convertImageToPdf(imagePath, pdfPath) {
  const image = await fs.promises.readFile(imagePath);
  const pdfDoc = await PDFDocument.create();
  const page = pdfDoc.addPage([400, 400]);

  // This will FAIL for PNG - embedJpg only works with JPEG
  const imageEmbed = await pdfDoc.embedJpg(image);

  const { width, height } = imageEmbed.scaleToFit(
    page.getWidth(),
    page.getHeight(),
  );

  page.drawImage(imageEmbed, {
    x: page.getWidth() / 2 - width / 2,
    y: page.getHeight() / 2 - height / 2,
    width,
    height,
  });

  const pdfBytes = await pdfDoc.save();
  await fs.promises.writeFile(pdfPath, pdfBytes);
}

convertImageToPdf("input.png", "output-png.pdf")
  .then(() => console.log("PNG converted successfully!"))
  .catch((error) => console.error("Error with PNG:", error.message));
