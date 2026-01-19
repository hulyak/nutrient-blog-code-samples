<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Signatures in Laravel</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .hero {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .card h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }

        .card p {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .card ul {
            list-style: none;
            margin-bottom: 25px;
        }

        .card li {
            padding: 8px 0;
            color: #555;
            font-size: 0.9rem;
            border-bottom: 1px solid #eee;
        }

        .card li:last-child {
            border-bottom: none;
        }

        .card li::before {
            content: "✓ ";
            color: #27ae60;
            font-weight: bold;
        }

        .card .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .card .badge.basic {
            background: #fff3cd;
            color: #856404;
        }

        .card .badge.recommended {
            background: #d4edda;
            color: #155724;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s, transform 0.2s;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: scale(1.02);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
            transform: scale(1.02);
        }

        .setup-info {
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
            color: white;
        }

        .setup-info h3 {
            margin-bottom: 15px;
        }

        .setup-info code {
            background: rgba(0,0,0,0.2);
            padding: 3px 8px;
            border-radius: 4px;
            font-family: monospace;
        }

        .setup-info ol {
            margin-left: 20px;
        }

        .setup-info li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Digital Signatures in Laravel</h1>
            <p>Two approaches to adding signatures to PDF documents</p>
        </div>

        <div class="cards">
            <div class="card">
                <div class="card-icon">🖼️</div>
                <span class="badge basic">Basic Approach</span>
                <h2>Intervention Image</h2>
                <p>
                    Overlay signature images onto PDF pages. Good for visual signatures
                    but lacks cryptographic security.
                </p>
                <ul>
                    <li>Image overlay on PDF pages</li>
                    <li>Position control (X, Y coordinates)</li>
                    <li>Converts pages to images</li>
                    <li>Exports to PDF using FPDF</li>
                </ul>
                <a href="{{ route('signature.form') }}" class="btn btn-secondary">Try Intervention Image</a>
            </div>

            <div class="card">
                <div class="card-icon">🔐</div>
                <span class="badge recommended">Recommended</span>
                <h2>Nutrient Web SDK</h2>
                <p>
                    Secure digital signatures using X.509 certificates and PKCS#7 format.
                    Industry-standard cryptographic signatures.
                </p>
                <ul>
                    <li>X.509 certificate-based</li>
                    <li>PKCS#7 signature format</li>
                    <li>Timestamp validation</li>
                    <li>Regulatory compliance</li>
                </ul>
                <a href="{{ route('nutrient.viewer') }}" class="btn btn-primary">Try Nutrient SDK</a>
            </div>
        </div>

        <div class="setup-info">
            <h3>Quick Setup</h3>
            <ol>
                <li>Place a PDF file named <code>document.pdf</code> in the <code>public</code> folder</li>
                <li>For Nutrient signatures, generate certificates:
                    <br><code>openssl req -x509 -sha256 -nodes -newkey rsa:2048 -keyout public/private-key.pem -out public/cert.pem</code>
                </li>
                <li>Start the server: <code>php artisan serve</code></li>
            </ol>
        </div>
    </div>
</body>
</html>
