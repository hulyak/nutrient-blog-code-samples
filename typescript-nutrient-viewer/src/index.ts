import NutrientViewer from '@nutrient-sdk/viewer';
import * as forge from 'node-forge';

function generatePKCS7({
    fileContents,
}: {
    fileContents: ArrayBuffer | null;
}): Promise<ArrayBuffer> {
    // Fetch the certificate and private key.
    const certificatePromise = fetch('cert.pem').then((response) =>
        response.text(),
    );
    const privateKeyPromise = fetch('private-key.pem').then((response) =>
        response.text(),
    );

    return new Promise((resolve, reject) => {
        Promise.all([certificatePromise, privateKeyPromise])
            .then(([certificatePem, privateKeyPem]) => {
                // Parse the certificate and private key using Forge.js.
                const certificate = forge.pki.certificateFromPem(
                    certificatePem,
                );
                const privateKey = forge.pki.privateKeyFromPem(
                    privateKeyPem,
                );

                // Create a PKCS7 signature.
                const p7 = forge.pkcs7.createSignedData();
                if (!fileContents) {
                    throw new Error('No file contents provided.');
                }
                const buffer = forge.util.createBuffer(fileContents);
                p7.content = buffer.getBytes();
                p7.addCertificate(certificate);

                // Add the signer information.
                p7.addSigner({
                    key: privateKey,
                    certificate: certificate,
                    digestAlgorithm: forge.pki.oids['sha256'],
                    authenticatedAttributes: [
                        {
                            type: forge.pki.oids['contentType'],
                            value: forge.pki.oids['data'],
                        },
                        {
                            type: forge.pki.oids['messageDigest'],
                        },
                        {
                            type: forge.pki.oids['signingTime'],
                            value: new Date().toISOString(),
                        },
                    ],
                });

                // Sign the data.
                p7.sign({ detached: true });

                // Convert the result to an `ArrayBuffer`.
                const result = stringToArrayBuffer(
                    forge.asn1.toDer(p7.toAsn1()).getBytes(),
                );

                resolve(result);
            })
            .catch(reject);
    });
}

function stringToArrayBuffer(binaryString: string): ArrayBuffer {
    const buffer = new ArrayBuffer(binaryString.length);
    let bufferView = new Uint8Array(buffer);

    for (let i = 0, len = binaryString.length; i < len; i++) {
        bufferView[i] = binaryString.charCodeAt(i);
    }

    return buffer;
}

function load(document: string) {
    console.log(`Loading ${document}...`);
    NutrientViewer.load({
        document,
        container: '.container',
    })
        .then((instance) => {
            console.log('Nutrient loaded', instance);
            // Sign the document when Nutrient is loaded.
            instance
                .signDocument(null, generatePKCS7)
                .then(() => {
                    console.log('Document signed.');
                })
                .catch((error: Error) => {
                    console.error(
                        'The document could not be signed.',
                        error,
                    );
                });
        })
        .catch(console.error);
}

load('example.pdf');
