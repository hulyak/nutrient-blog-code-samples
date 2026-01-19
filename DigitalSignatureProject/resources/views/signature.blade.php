<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Signature - Intervention Image Approach</title>
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
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h1 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .nav-links {
            margin-bottom: 20px;
        }

        .nav-links a {
            color: #3498db;
            text-decoration: none;
            margin-right: 20px;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert a {
            color: inherit;
            font-weight: bold;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="file"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="number"] {
            max-width: 150px;
        }

        .inline-inputs {
            display: flex;
            gap: 20px;
        }

        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        button:hover {
            background: #2980b9;
        }

        .pages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .page-card {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .page-card img {
            width: 100%;
            height: auto;
            display: block;
        }

        .page-card .placeholder {
            width: 100%;
            height: 300px;
            background: #e9e9e9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }

        .page-card .page-label {
            padding: 10px;
            text-align: center;
            font-weight: 500;
            background: #fff;
            border-top: 1px solid #e0e0e0;
        }

        .info-box {
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .info-box ul {
            margin-left: 20px;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
        }

        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-links">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('nutrient.viewer') }}">Nutrient Digital Signatures</a>
        </div>

        <h1>Upload Your Signature</h1>
        <p class="subtitle">Intervention Image Approach - Basic image overlay on PDF pages</p>

        @if(session('success'))
            <div class="alert alert-success">
                <p>{{ session('success') }}</p>
                @if(session('signed_image'))
                    <p><a href="{{ session('signed_image') }}" target="_blank">View Signed Image</a></p>
                @endif
                @if(session('signed_pdf'))
                    <p><a href="{{ session('signed_pdf') }}" target="_blank">Download Signed PDF</a></p>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="warning-box">
            <strong>Note:</strong> This approach creates a visual signature overlay, not a cryptographic digital signature.
            For secure, legally-binding digital signatures, use the <a href="{{ route('nutrient.viewer') }}">Nutrient approach</a>.
        </div>

        <div class="card">
            <h2>Add Signature to PDF</h2>

            <form action="{{ route('save.signature') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="signature">Select your signature image (PNG/JPG):</label>
                    <input type="file" name="signature" id="signature" accept="image/png,image/jpeg" required>
                </div>

                <div class="inline-inputs">
                    <div class="form-group">
                        <label for="page_number">Page Number:</label>
                        <input type="number" name="page_number" id="page_number" value="1" min="1">
                    </div>

                    <div class="form-group">
                        <label for="x_position">X Position (px):</label>
                        <input type="number" name="x_position" id="x_position" value="100" min="0">
                    </div>

                    <div class="form-group">
                        <label for="y_position">Y Position (px):</label>
                        <input type="number" name="y_position" id="y_position" value="100" min="0">
                    </div>
                </div>

                <button type="submit">Add Signature to PDF</button>
            </form>
        </div>

        <div class="info-box">
            <h3>How to use:</h3>
            <ul>
                <li>First, place a PDF named <code>document.pdf</code> in the <code>public</code> folder</li>
                <li>Visit <code>/signature</code> to generate page images (requires Imagick extension)</li>
                <li>Upload a signature image (transparent PNG works best)</li>
                <li>Adjust the position coordinates as needed</li>
                <li>The signed image and PDF will be saved in <code>public/signed/</code></li>
            </ul>
        </div>

        <div class="card">
            <h2>PDF Pages</h2>

            @if(count($images) > 0)
                <div class="pages-grid">
                    @foreach($images as $image)
                        <div class="page-card">
                            @if($image['url'])
                                <img src="{{ $image['url'] }}" alt="Page {{ $image['number'] }}">
                            @else
                                <div class="placeholder">
                                    No PDF loaded.<br>
                                    Place document.pdf in public/ and<br>
                                    visit /signature to generate images.
                                </div>
                            @endif
                            <div class="page-label">Page {{ $image['number'] }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No pages found. Please generate page images first.</p>
            @endif
        </div>
    </div>
</body>
</html>
