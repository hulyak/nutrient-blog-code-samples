const { Document, Packer, Paragraph, TextRun } = require("docx");
const fs = require("fs");

const doc = new Document({
  sections: [
    {
      properties: {},
      children: [
        new Paragraph({
          children: [
            new TextRun({
              text: "Test Document",
              bold: true,
              size: 48,
            }),
          ],
        }),
        new Paragraph({
          children: [
            new TextRun({
              text: "This is a test Word document created for testing DOCX to PDF conversion.",
              size: 24,
            }),
          ],
        }),
        new Paragraph({
          children: [
            new TextRun({
              text: "It contains some sample text to verify the conversion works correctly.",
              size: 24,
            }),
          ],
        }),
      ],
    },
  ],
});

Packer.toBuffer(doc).then((buffer) => {
  fs.writeFileSync("document.docx", buffer);
  console.log("document.docx created successfully!");
});
