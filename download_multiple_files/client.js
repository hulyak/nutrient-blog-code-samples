// Node.js client to test the multipart/form-data response
// Note: In a browser, you would use res.formData() directly

const http = require('http');

async function downloadMultipleFiles() {
    return new Promise((resolve, reject) => {
        const options = {
            hostname: 'localhost',
            port: 4000,
            path: '/download-multiple',
            method: 'GET',
            headers: {
                'Accept': 'multipart/form-data'
            }
        };

        const req = http.request(options, (res) => {
            console.log(`Status: ${res.statusCode}`);
            console.log(`Content-Type: ${res.headers['content-type']}`);

            const chunks = [];
            res.on('data', (chunk) => chunks.push(chunk));
            res.on('end', () => {
                const body = Buffer.concat(chunks);
                console.log(`\nReceived ${body.length} bytes`);

                // Parse the multipart response manually
                const contentType = res.headers['content-type'];
                const boundaryMatch = contentType.match(/boundary=(.+)/);
                if (boundaryMatch) {
                    const boundary = boundaryMatch[1];
                    console.log(`Boundary: ${boundary}`);

                    // Split by boundary and parse parts
                    const parts = body.toString('binary').split('--' + boundary);
                    console.log(`\nFound ${parts.length - 2} file parts:`); // -2 for first empty and last --

                    parts.forEach((part, index) => {
                        if (part.includes('filename=')) {
                            const filenameMatch = part.match(/filename="([^"]+)"/);
                            const contentTypeMatch = part.match(/Content-Type: ([^\r\n]+)/);
                            if (filenameMatch) {
                                console.log(`  - ${filenameMatch[1]} (${contentTypeMatch ? contentTypeMatch[1] : 'unknown type'})`);
                            }
                        }
                    });
                }

                resolve(body);
            });
        });

        req.on('error', (e) => {
            reject(e);
        });

        req.end();
    });
}

downloadMultipleFiles()
    .then(() => console.log('\nSuccess! Files received.'))
    .catch((err) => console.error('Error:', err.message));
