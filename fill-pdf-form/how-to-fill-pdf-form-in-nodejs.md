---
title: "How to Fill PDF Forms in Node.js"
description: "Learn how to fill PDF forms programmatically in Node.js using pdf-lib. Includes complete code examples and comparison with Nutrient's form filling SDK."
preview_image: "@/assets/images/blog/2023/how-to-fill-pdf-form-in-nodejs/article-header.png"
section: blog
author:
   - Hulya Masharipov
author_url:
   - https://www.linkedin.com/in/hulya-Masharipov/
date: 2023-08-16 10:00 UTC
categories:
- SDK
- Tutorials
products: Nutrient Web SDK
tags:
  - Web
  - Node.js
  - How To
published: true
secret: false
converted_from: "pspdfkit"
converted_at: "2025-05-29"
converted_by_git_ref: "3a2d1b973"
converted_from_path: "how-to-fill-pdf-form-in-nodejs.html.md.erb"
---

{% postTLDR %}
Learn how to programmatically fill PDF form fields using Node.js and pdf-lib. This tutorial covers fetching PDFs, accessing form fields by name, setting values, and saving the filled form. You'll also discover Nutrient's alternatives for UI-based and programmatic form filling.
{% /postTLDR %}

In this tutorial, you'll explore how to programmatically fill PDF form fields using Node.js and the [pdf-lib][] library. You'll use [`axios`][] to fetch a PDF file, and then you'll leverage the power of pdf-lib to fill in PDF form fields with the desired values. By the end of this tutorial, you'll be able to automate the process of filling PDF forms and generate customized PDF files effortlessly.

## Prerequisites

Before you begin, make sure you have the following installed:

- [Node.js][] — It's best to use the latest long-term support (LTS) version or a stable version.
- [npm][] — Node Package Manager comes bundled with Node.js, so it'll be installed by default.

## Setting Up the Project

1. To get started, create a new directory for your project and initialize it as a Node.js project by running the following commands in your terminal:

```bash
mkdir fill-pdf-form
cd fill-pdf-form
npm init -y
```

2. In this tutorial, you'll use pdf-lib, a powerful JavaScript library for PDF manipulation. Install it by running the following command:

```bash
npm install pdf-lib
```

3. Install the `axios` library, which will help you fetch the PDF file:

```bash
npm install axios
```

4. Create a new file in the project directory called `index.js`, and open it in a code editor.

## Using the pdf-lib Library

1. Import the required modules by adding the following code at the top of the `index.js` file:

```javascript
const { PDFDocument } = require('pdf-lib');
const fs = require('fs');
const axios = require('axios');
```

The pdf-lib library provides capabilities for manipulating existing PDF files, such as filling form fields. You also need the `fs` module to perform file system operations, and the `axios` library to make HTTP requests.

2. Define the `fillPdfForm` function, which will handle filling the PDF form fields. Copy and paste the following code below the module imports:

```js
async function fillPdfForm(input) {
    const response = await axios.get(input, {
        responseType: 'arraybuffer',
    });
    const pdfBytes = response.data;
    const pdfDoc = await PDFDocument.load(pdfBytes);
    const form = pdfDoc.getForm();

    // Get the form fields by their names.
    const nameField = form.getTextField('CharacterName 2');
    const ageField = form.getTextField('Age');
    const heightField = form.getTextField('Height');
    const weightField = form.getTextField('Weight');
    const eyesField = form.getTextField('Eyes');
    const skinField = form.getTextField('Skin');
    const hairField = form.getTextField('Hair');
    const alliesField = form.getTextField('Allies');
    const factionField = form.getTextField('FactionName');
    const backstoryField = form.getTextField('Backstory');
    const traitsField = form.getTextField('Feat+Traits');
    const treasureField = form.getTextField('Treasure');

    // Set the values for the form fields.
    nameField.setText('Template');
    ageField.setText('24 years');
    heightField.setText(`5' 1"`);
    weightField.setText('196 lbs');
    eyesField.setText('blue');
    skinField.setText('white');
    hairField.setText('brown');
    alliesField.setText(
        [
            `Allies:`,
            `  • Princess Daisy`,
            `  • Princess Peach`,
            `  • Rosalina`,
            `  • Geno`,
            `  • Luigi`,
            `  • Donkey Kong`,
            `  • Yoshi`,
            `  • Diddy Kong`,
            ``,
            `Organizations:`,
            `  • Italian Plumbers Association`,
        ].join('\n'),
    );

    factionField.setText(`Mario's Emblem`);
    backstoryField.setText(
        [
            `Mario is a fictional character in the Mario video game franchise,`,
            `owned by Nintendo and created by Japanese video game designer Shigeru`,
            `Miyamoto. Serving as the company's mascot and the eponymous`,
            `protagonist of the series, Mario has appeared in over 200 video games`,
            `since his creation. Depicted as a short, pudgy, Italian plumber who`,
            `resides in the Mushroom Kingdom, his adventures generally center`,
            `upon rescuing Princess Peach from the Koopa villain Bowser. His`,
            `younger brother and sidekick is Luigi.`,
        ].join('\n'),
    );
    traitsField.setText(
        [
            `Mario can use three basic power-ups:`,
            `  • Super Mushroom: makes Mario grow larger`,
            `  • Fire Flower: allows Mario to throw fireballs`,
            `  • Starman: grants temporary invincibility`,
        ].join('\n'),
    );
    treasureField.setText(
        ['• Gold coins', '• Treasure chests'].join('\n'),
    );

    return pdfDoc;
}
```

The `fillPdfForm` function is an asynchronous function that fills a PDF form. It takes the `input` parameter (the URL of the PDF file to be filled). It fetches the PDF file, loads it into a `pdfDoc` object, retrieves the form, and sets the values for the form fields using their names and the `setText` method.

3. Define the `saveFilledForm` function, which will save the filled PDF form to a new file:

```js
async function saveFilledForm(pdfDoc, output) {
    const filledFormBytes = await pdfDoc.save();
    fs.writeFileSync(output, filledFormBytes);
    console.log('Filled form saved successfully!');
}
```

This function takes the `pdfDoc` object and the `output` file name as parameters. It calls `pdfDoc.save()` to obtain the modified PDF bytes and writes them to a file using `fs.writeFileSync`.

4. Define the `main` function, which will orchestrate the process of filling and saving the PDF form:

```js
async function main() {
    const input = 'https://pdf-lib.js.org/assets/dod_character.pdf';
    const output = 'output.pdf';

    const pdfDoc = await fillPdfForm(input);
    await saveFilledForm(pdfDoc, output);
}

main().catch((err) => console.error('Error:', err));
```

This function is the entry point of the program. It defines the input URL and output file name, calls `fillPdfForm` to fill the PDF form and obtain the modified `pdfDoc` object, and then calls `saveFilledForm` to save the filled form to a new file.

Ensure you have write permissions for the current directory. The filled form will be saved as `output.pdf`.

5. To run the code, execute the following command in your terminal:

```bash
node index.js
```

Now, you'll see the `output.pdf` file in your current directory. Open it to see the filled form.

![Filled PDF character sheet form showing Mario's backstory, traits, and allies](@/assets/images/blog/2023/how-to-fill-pdf-form-in-nodejs/output.png)

## Downsides of Using pdf-lib

While the pdf-lib library is a powerful tool for working with PDF documents, including form filling, it does have a few downsides and limitations:

1. **Limited User Interface (UI) Interactivity** — The pdf-lib library primarily focuses on programmatic manipulation of PDF documents, and it doesn't provide built-in UI components or interactivity for form filling. As a result, users cannot directly type into the form fields within the application's UI; the form fields need to be programmatically filled using code, as demonstrated in the example.
2. **Lack of Real-Time Updates** — Since the form fields are filled programmatically on the server or within the application code, there's no real-time update or synchronization with the UI. If the user wants to see their input reflected in the filled form fields, they need to generate and download the updated PDF file.

## Form Filling with Nutrient

Nutrient offers comprehensive [form filling capabilities][form filling], providing both a user-friendly interface and programmable options.

- **UI Form Filling** — Nutrient's prebuilt UI components allow users to easily navigate through and interact with PDF forms. They can fill in text fields, select options from dropdown menus, and interact with checkboxes and radio buttons. Check out the [demo][] to see it in action.
- [Programmatic Form Filling][programmatic] — Nutrient Web SDK offers versatile programmatic form filling options.
  - **PSPDFKit Server** — Easily persist, restore, and synchronize form field values across devices without building forms yourself.
  - **XFDF** — Exchange form field data with other PDF readers and editors seamlessly.
  - **Instant JSON** — Efficiently export and import changes made to form fields.
  - **Manual API** — Full control over extracting, saving, and manipulating form field values.

In addition, Nutrient also provides an option for creating PDF forms:

- [PDF Form Creator][] — Simplify PDF form creation with a point-and-click UI. You can create PDF forms from scratch using an intuitive UI or via the API. Convert static forms into fillable forms, or modify existing forms by letting your users create, edit, and remove form fields in a PDF.

These options provided by Nutrient offer flexibility for custom workflows, data interoperability, and efficient form filling and form creation processes.

## Conclusion

In this tutorial, you learned how to programmatically fill out PDF forms using Node.js and the pdf-lib library. By automating the process of filling out forms, you can save time and enhance efficiency in your workflows. You also learned about some of the limitations of using pdf-lib for form filling and explored how Nutrient can be used as an alternative solution.

To learn more about Nutrient Web SDK, start your [free trial][try]. Or, [launch our demo][demo] to see our viewer in action.

## FAQ

{% accordion faqSchema=true %}
{% accordionitem title="How can I fill PDF forms using Node.js?" %}
You can use the pdf-lib library in combination with Node.js to fill PDF forms programmatically.
{% /accordionitem %}

{% accordionitem
   title="Do I need any additional tools to fill PDF forms in Node.js?" %}
Yes, you need to install pdf-lib and optionally `axios` if you're fetching PDFs from external sources.
{% /accordionitem %}

{% accordionitem
   title="Can I fill forms with multiple fields using pdf-lib?" %}
Yes, you can easily fill multiple form fields by targeting them using their field names and setting the desired values.
{% /accordionitem %}

{% accordionitem
   title="Is pdf-lib suitable for filling forms with user interaction?" %}
No, pdf-lib is used for programmatic form filling. It does not offer user interface components for direct interaction with the form fields.
{% /accordionitem %}

{% accordionitem
   title="Are there alternatives to pdf-lib for PDF form filling?" %}
Yes, Nutrient offers advanced form filling options, including UI components and programmatic solutions.
{% /accordionitem %}
{% /accordion %}

[node.js]: https://nodejs.org/en
[pdf-lib]: https://pdf-lib.js.org/#examples
[`axios`]: https://www.npmjs.com/package/axios
[npm]: https://docs.npmjs.com/about-npm
[form filling]: /guides/web/forms/introduction-to-forms/
[demo]: /demo/pdf-form-fill/
[programmatic]: /guides/web/forms/form-filling/
[try]: /try
[pdf form creator]: /sdk/solutions/forms
