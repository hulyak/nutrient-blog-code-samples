const fs = require("fs");
const { PDFDocument } = require("pdf-lib");

async function convertImageToPdf(imagePath, pdfPath) {
  // Read the image file asynchronously.
  const image = await fs.promises.readFile(imagePath);

  // Create a new PDF document.
  const pdfDoc = await PDFDocument.create();

  // Add a new page to the PDF document with a dimension of 400×400 points.
  const page = pdfDoc.addPage([400, 400]);

  // Embed the image into the PDF document.
  // Use embedPng() for PNG files, embedJpg() for JPEG files.
  const imageEmbed = imagePath.toLowerCase().endsWith(".png")
    ? await pdfDoc.embedPng(image)
    : await pdfDoc.embedJpg(image);

  // Scale the image to fit within the page dimensions while preserving aspect ratio.
  const { width, height } = imageEmbed.scaleToFit(
    page.getWidth(),
    page.getHeight(),
  );

  // Draw the image on the PDF page.
  page.drawImage(imageEmbed, {
    x: page.getWidth() / 2 - width / 2, // Center the image horizontally.
    y: page.getHeight() / 2 - height / 2, // Center the image vertically.
    width,
    height,
  });

  // Save the PDF document as bytes.
  const pdfBytes = await pdfDoc.save();

  // Write the PDF bytes to a file asynchronously.
  await fs.promises.writeFile(pdfPath, pdfBytes);
}

// Test with both JPEG and PNG
async function runTests() {
  try {
    await convertImageToPdf("input.jpg", "fixed-output-jpg.pdf");
    console.log("✓ JPEG converted successfully!");

    await convertImageToPdf("input.png", "fixed-output-png.pdf");
    console.log("✓ PNG converted successfully!");

    console.log("\nAll tests passed!");
  } catch (error) {
    console.error("Error:", error.message);
  }
}

runTests();
