<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Signatures - Nutrient Web SDK</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.2rem;
            font-weight: 500;
        }

        .header-links a {
            color: #ecf0f1;
            text-decoration: none;
            margin-left: 20px;
            font-size: 0.9rem;
        }

        .header-links a:hover {
            color: #3498db;
        }

        .toolbar {
            background: #34495e;
            padding: 10px 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .toolbar button {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .toolbar button:hover {
            background: #2980b9;
        }

        .toolbar button:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .toolbar .status {
            color: #bdc3c7;
            font-size: 0.9rem;
            margin-left: auto;
        }

        .toolbar .status.success {
            color: #2ecc71;
        }

        .toolbar .status.error {
            color: #e74c3c;
        }

        #nutrient {
            height: calc(100vh - 110px);
            width: 100%;
        }

        .no-pdf-message {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 110px);
            background: #ecf0f1;
            color: #7f8c8d;
        }

        .no-pdf-message h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .no-pdf-message p {
            margin-bottom: 10px;
        }

        .no-pdf-message code {
            background: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-family: monospace;
        }

        .info-panel {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 15px;
            max-width: 350px;
            display: none;
            z-index: 1000;
        }

        .info-panel.visible {
            display: block;
        }

        .info-panel h3 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .info-panel p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 8px;
        }

        .info-panel .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nutrient Digital Signatures</h1>
        <div class="header-links">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('signature.form') }}">Intervention Image Approach</a>
        </div>
    </div>

    <div class="toolbar">
        <button id="signBtn" onclick="signDocument()">Sign Document</button>
        <button id="infoBtn" onclick="toggleInfo()">About Digital Signatures</button>
        <span id="status" class="status">Ready</span>
    </div>

    <div id="nutrient"></div>

    <div id="infoPanel" class="info-panel">
        <button class="close-btn" onclick="toggleInfo()">&times;</button>
        <h3>About Digital Signatures</h3>
        <p>
            This demo uses X.509 certificates and PKCS#7 format to create
            cryptographically secure digital signatures.
        </p>
        <p>
            <strong>Requirements:</strong><br>
            - cert.pem (X.509 certificate)<br>
            - private-key.pem (RSA private key)
        </p>
        <p>
            Generate them with OpenSSL:<br>
            <code style="font-size: 0.8rem;">openssl req -x509 -sha256 -nodes -newkey rsa:2048 -keyout private-key.pem -out cert.pem</code>
        </p>
    </div>

    <!-- Include Forge library for cryptography -->
    <script src="https://cdn.jsdelivr.net/npm/node-forge@1.0.0/dist/forge.min.js"></script>

    <!-- Include Nutrient Web SDK -->
    <script src="{{ asset('assets/nutrient-viewer.js') }}"></script>

    <script>
        let nutrientInstance = null;

        /**
         * Convert a binary string to an ArrayBuffer.
         */
        function stringToArrayBuffer(binaryString) {
            const buffer = new ArrayBuffer(binaryString.length);
            let bufferView = new Uint8Array(buffer);

            for (let i = 0, len = binaryString.length; i < len; i++) {
                bufferView[i] = binaryString.charCodeAt(i);
            }

            return buffer;
        }

        /**
         * Generate a PKCS#7 digital signature using Forge.
         * This creates a valid cryptographic signature that can be embedded in PDFs.
         */
        function generatePKCS7({ fileContents }) {
            const certificatePromise = fetch("{{ asset('cert.pem') }}").then((response) => {
                if (!response.ok) {
                    throw new Error('Certificate file not found. Please place cert.pem in the public folder.');
                }
                return response.text();
            });

            const privateKeyPromise = fetch("{{ asset('private-key.pem') }}").then((response) => {
                if (!response.ok) {
                    throw new Error('Private key file not found. Please place private-key.pem in the public folder.');
                }
                return response.text();
            });

            return new Promise((resolve, reject) => {
                Promise.all([certificatePromise, privateKeyPromise])
                    .then(([certificatePem, privateKeyPem]) => {
                        try {
                            // Parse the certificate and private key
                            const certificate = forge.pki.certificateFromPem(certificatePem);
                            const privateKey = forge.pki.privateKeyFromPem(privateKeyPem);

                            // Create PKCS#7 signed data structure
                            const p7 = forge.pkcs7.createSignedData();
                            p7.content = new forge.util.ByteBuffer(fileContents);
                            p7.addCertificate(certificate);

                            // Add the signer with SHA-256 digest
                            p7.addSigner({
                                key: privateKey,
                                certificate: certificate,
                                digestAlgorithm: forge.pki.oids.sha256,
                                authenticatedAttributes: [
                                    {
                                        type: forge.pki.oids.contentType,
                                        value: forge.pki.oids.data,
                                    },
                                    {
                                        type: forge.pki.oids.messageDigest,
                                    },
                                    {
                                        type: forge.pki.oids.signingTime,
                                        value: new Date(),
                                    },
                                ],
                            });

                            // Sign the data (detached signature)
                            p7.sign({ detached: true });

                            // Convert to DER format
                            const result = stringToArrayBuffer(
                                forge.asn1.toDer(p7.toAsn1()).getBytes()
                            );

                            resolve(result);
                        } catch (error) {
                            reject(new Error('Failed to create signature: ' + error.message));
                        }
                    })
                    .catch(reject);
            });
        }

        /**
         * Sign the currently loaded document.
         */
        function signDocument() {
            if (!nutrientInstance) {
                updateStatus('No document loaded', 'error');
                return;
            }

            updateStatus('Signing document...');
            document.getElementById('signBtn').disabled = true;

            nutrientInstance
                .signDocument(null, generatePKCS7)
                .then(() => {
                    updateStatus('Document signed successfully!', 'success');
                    console.log('Document signed successfully');
                })
                .catch((error) => {
                    updateStatus('Signing failed: ' + error.message, 'error');
                    console.error('The document could not be signed:', error);
                })
                .finally(() => {
                    document.getElementById('signBtn').disabled = false;
                });
        }

        /**
         * Update the status message in the toolbar.
         */
        function updateStatus(message, type = '') {
            const statusEl = document.getElementById('status');
            statusEl.textContent = message;
            statusEl.className = 'status ' + type;
        }

        /**
         * Toggle the info panel visibility.
         */
        function toggleInfo() {
            const panel = document.getElementById('infoPanel');
            panel.classList.toggle('visible');
        }

        /**
         * Initialize Nutrient Web SDK.
         */
        function initializeNutrient() {
            // Check if document.pdf exists
            fetch("{{ asset('document.pdf') }}", { method: 'HEAD' })
                .then(response => {
                    if (!response.ok) {
                        showNoPdfMessage();
                        return;
                    }

                    // Load Nutrient with the document
                    NutrientViewer.load({
                        container: "#nutrient",
                        document: "{{ asset('document.pdf') }}"
                    })
                    .then(function(instance) {
                        nutrientInstance = instance;
                        console.log("Nutrient loaded successfully", instance);
                        updateStatus('Document loaded - Ready to sign');
                    })
                    .catch(function(error) {
                        console.error("Failed to load Nutrient:", error.message);
                        updateStatus('Failed to load viewer: ' + error.message, 'error');
                    });
                })
                .catch(error => {
                    showNoPdfMessage();
                });
        }

        /**
         * Show message when no PDF is available.
         */
        function showNoPdfMessage() {
            document.getElementById('nutrient').innerHTML = `
                <div class="no-pdf-message">
                    <h2>No PDF Document Found</h2>
                    <p>Please place a PDF file named <code>document.pdf</code> in the <code>public</code> folder.</p>
                    <p>Also ensure you have generated certificates:</p>
                    <p><code>openssl req -x509 -sha256 -nodes -newkey rsa:2048 -keyout public/private-key.pem -out public/cert.pem</code></p>
                </div>
            `;
            updateStatus('No document loaded', 'error');
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', initializeNutrient);
    </script>
</body>
</html>
