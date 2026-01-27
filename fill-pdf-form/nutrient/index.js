const Nutrient = require('@nutrient-sdk/node');
const fs = require('fs');
const axios = require('axios');

async function fillPdfForm(input) {
    // Download the PDF file
    const response = await axios.get(input, {
        responseType: 'arraybuffer',
    });
    const pdfBuffer = Buffer.from(response.data);

    // Load the document with Nutrient
    const instance = await Nutrient.load({
        document: pdfBuffer,
        licenseKey: 'pdf_live_TK4VeBhzh4K7hq0wBjJuvfiekbOb62TqwjGycFYqA5Y',
    });

    // Define form field values
    const formFieldValues = {
        'CharacterName 2': 'Template',
        'Age': '24 years',
        'Height': `5' 1"`,
        'Weight': '196 lbs',
        'Eyes': 'blue',
        'Skin': 'white',
        'Hair': 'brown',
        'Allies': [
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
        'FactionName': `Mario's Emblem`,
        'Backstory': [
            `Mario is a fictional character in the Mario video game franchise,`,
            `owned by Nintendo and created by Japanese video game designer Shigeru`,
            `Miyamoto. Serving as the company's mascot and the eponymous`,
            `protagonist of the series, Mario has appeared in over 200 video games`,
            `since his creation. Depicted as a short, pudgy, Italian plumber who`,
            `resides in the Mushroom Kingdom, his adventures generally center`,
            `upon rescuing Princess Peach from the Koopa villain Bowser. His`,
            `younger brother and sidekick is Luigi.`,
        ].join('\n'),
        'Feat+Traits': [
            `Mario can use three basic power-ups:`,
            `  • Super Mushroom: makes Mario grow larger`,
            `  • Fire Flower: allows Mario to throw fireballs`,
            `  • Starman: grants temporary invincibility`,
        ].join('\n'),
        'Treasure': ['• Gold coins', '• Treasure chests'].join('\n'),
    };

    // Get all form fields and fill them
    const formFields = await instance.getFormFields();

    for (const field of formFields) {
        const fieldName = field.name;
        if (formFieldValues[fieldName] !== undefined) {
            const updatedField = field.set('value', formFieldValues[fieldName]);
            await instance.update(updatedField);
        }
    }

    return instance;
}

async function saveFilledForm(instance, output) {
    // Export the filled PDF
    const pdfBuffer = await instance.exportPDF();
    fs.writeFileSync(output, Buffer.from(pdfBuffer));
    console.log('Filled form saved successfully with Nutrient!');

    // Close the instance
    await instance.close();
}

async function main() {
    const input = 'https://pdf-lib.js.org/assets/dod_character.pdf';
    const output = 'output.pdf';

    const instance = await fillPdfForm(input);
    await saveFilledForm(instance, output);
}

main().catch((err) => console.error('Error:', err));
