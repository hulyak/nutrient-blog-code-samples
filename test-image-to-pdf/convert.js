const fs = require("fs");
const { PDFDocument, rgb } = require("pdf-lib");

async function convertImageToPdf(imagePath, pdfPath) {
  // Read the image file asynchronously.
  const image = await fs.promises.readFile(imagePath);

  // Create a new PDF document.
  const pdfDoc = await PDFDocument.create();

  // Add a new page to the PDF document with a dimension of 400×400 points.
  const page = pdfDoc.addPage([400, 400]);

  // Embed the image into the PDF document.
  const imageEmbed = await pdfDoc.embedJpg(image);

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
    color: rgb(0, 0, 0), // Set the image color to black.
  });

  // Save the PDF document as bytes.
  const pdfBytes = await pdfDoc.save();

  // Write the PDF bytes to a file asynchronously.
  await fs.promises.writeFile(pdfPath, pdfBytes);
}

// Call the conversion function with input and output file paths.
convertImageToPdf("input.jpg", "output.pdf")
  .then(() => {
    console.log("Image converted to PDF successfully!");
  })
  .catch((error) => {
    console.error("Error converting image to PDF:", error);
  });
