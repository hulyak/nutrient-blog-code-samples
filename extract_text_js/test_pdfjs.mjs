import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf.mjs';

// Test the extractText function from the tutorial
async function extractText(pdfUrl) {
    const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
    const totalPageCount = pdf.numPages;
    console.log('Total pages:', totalPageCount);

    const texts = [];
    for (let currentPage = 1; currentPage <= totalPageCount; currentPage++) {
        const page = await pdf.getPage(currentPage);
        const textContent = await page.getTextContent();
        const pageText = textContent.items.map(s => s.str).join('');
        texts.push(pageText);
    }

    return texts.join('');
}

// Test with the example PDF from the tutorial
const url = 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf';

console.log('Testing PDF.js text extraction...');
console.log('PDF URL:', url);
console.log('');

try {
    const text = await extractText(url);
    console.log('SUCCESS! Extracted', text.length, 'characters');
    console.log('');
    console.log('First 500 characters:');
    console.log('---');
    console.log(text.substring(0, 500));
    console.log('---');
} catch (error) {
    console.error('ERROR:', error.message);
}
