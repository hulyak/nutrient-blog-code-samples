import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf.mjs';

async function extractText(pdfUrl) {
    const loadingTask = pdfjsLib.getDocument(pdfUrl);
    const pdf = await loadingTask.promise;

    const totalPageCount = pdf.numPages;
    console.log(`Total pages: ${totalPageCount}`);

    let allText = [];

    for (let currentPage = 1; currentPage <= totalPageCount; currentPage++) {
        const page = await pdf.getPage(currentPage);
        const textContent = await page.getTextContent();
        const pageText = textContent.items
            .map(item => item.str)
            .join('');
        allText.push(pageText);
        console.log(`Page ${currentPage} extracted (${pageText.length} chars)`);
    }

    return allText.join('');
}

// Example PDF file from Mozilla
const url = 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf';

console.log('Starting PDF text extraction...\n');

try {
    const text = await extractText(url);
    console.log('\n--- Extracted Text Preview (first 1500 chars) ---\n');
    console.log(text.substring(0, 1500));
    console.log('\n... (truncated)');
    console.log(`\nTotal extracted text length: ${text.length} characters`);
    console.log('\n✓ PDF text extraction successful!');
} catch (error) {
    console.error('Error extracting text:', error);
}
