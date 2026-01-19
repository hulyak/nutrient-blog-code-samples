<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SignatureController extends Controller
{
    /**
     * Show the signature form with PDF pages displayed as images.
     *
     * Note: For PDF to image conversion, you need either:
     * - Imagick PHP extension with Ghostscript, OR
     * - A pre-converted set of page images
     *
     * This demo assumes page images already exist in public/pages/
     */
    public function showForm()
    {
        // Get existing page images from public/pages directory
        $pagesPath = public_path('pages');
        $images = [];

        if (is_dir($pagesPath)) {
            $files = glob($pagesPath . '/page*.{jpg,png}', GLOB_BRACE);
            sort($files, SORT_NATURAL);

            foreach ($files as $index => $file) {
                $images[] = [
                    'number' => $index + 1,
                    'url' => asset('pages/' . basename($file))
                ];
            }
        }

        // If no images found, show placeholder
        if (empty($images)) {
            $images[] = [
                'number' => 1,
                'url' => null
            ];
        }

        return view('signature', compact('images'));
    }

    /**
     * Generate images from PDF pages.
     *
     * IMPORTANT: This requires the Imagick PHP extension.
     * If Imagick is not available, you can:
     * 1. Install Imagick: pecl install imagick
     * 2. Use an external service/API
     * 3. Pre-convert PDFs manually
     */
    public function generateImages(Request $request)
    {
        $pdfPath = public_path('document.pdf');

        if (!file_exists($pdfPath)) {
            return response()->json([
                'error' => 'PDF file not found. Please place document.pdf in the public folder.'
            ], 404);
        }

        // Check if Imagick is available
        if (!extension_loaded('imagick')) {
            return response()->json([
                'error' => 'Imagick extension is not installed. Please install it to convert PDFs to images.',
                'instructions' => [
                    'macOS' => 'brew install imagemagick && pecl install imagick',
                    'Ubuntu' => 'apt-get install php-imagick',
                    'Windows' => 'Download from PECL and enable in php.ini'
                ]
            ], 500);
        }

        try {
            // Create pages directory if it doesn't exist
            $outputDir = public_path('pages');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Use Imagick directly to convert PDF pages
            $imagick = new \Imagick();
            $imagick->setResolution(150, 150);
            $imagick->readImage($pdfPath);

            $pageCount = $imagick->getNumberImages();
            $generatedImages = [];

            for ($i = 0; $i < $pageCount; $i++) {
                $imagick->setIteratorIndex($i);
                $imagick->setImageFormat('jpg');
                $imagick->setImageCompressionQuality(85);

                $outputPath = $outputDir . "/page" . ($i + 1) . ".jpg";
                $imagick->writeImage($outputPath);

                $generatedImages[] = asset('pages/page' . ($i + 1) . '.jpg');
            }

            $imagick->clear();
            $imagick->destroy();

            return response()->json([
                'message' => 'Images generated successfully',
                'pages' => $pageCount,
                'images' => $generatedImages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to convert PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save the signature by overlaying it on a PDF page image.
     * Then optionally convert back to PDF using FPDF.
     */
    public function saveSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'page_number' => 'nullable|integer|min:1',
            'x_position' => 'nullable|integer|min:0',
            'y_position' => 'nullable|integer|min:0',
        ]);

        $pageNumber = $request->input('page_number', 1);
        $xPosition = $request->input('x_position', 100);
        $yPosition = $request->input('y_position', 100);

        // Check if the page image exists
        $pageImagePath = public_path("pages/page{$pageNumber}.jpg");

        if (!file_exists($pageImagePath)) {
            // For demo purposes, create a placeholder page
            $pageImagePath = $this->createPlaceholderPage($pageNumber);
        }

        try {
            // Initialize Intervention Image with GD driver
            $manager = new ImageManager(new Driver());

            // Load the page image
            $pageImage = $manager->read($pageImagePath);

            // Load and process the signature
            $signatureFile = $request->file('signature');
            $signatureImage = $manager->read($signatureFile->getPathname());

            // Resize signature to reasonable dimensions (max 200px wide)
            $signatureImage->scale(width: 200);

            // Insert signature onto page at specified position
            $pageImage->place($signatureImage, 'top-left', $xPosition, $yPosition);

            // Create signed directory if it doesn't exist
            $signedDir = public_path('signed');
            if (!is_dir($signedDir)) {
                mkdir($signedDir, 0755, true);
            }

            // Save the signed page image
            $signedImagePath = $signedDir . "/signed_page{$pageNumber}.jpg";
            $pageImage->save($signedImagePath, quality: 90);

            // Optionally create a PDF from the signed image
            $pdfPath = $this->createPdfFromImage($signedImagePath, $pageNumber);

            return redirect()->route('signature.form')->with([
                'success' => 'Signature added successfully!',
                'signed_image' => asset("signed/signed_page{$pageNumber}.jpg"),
                'signed_pdf' => $pdfPath ? asset("signed/signed_page{$pageNumber}.pdf") : null
            ]);

        } catch (\Exception $e) {
            return redirect()->route('signature.form')->with([
                'error' => 'Failed to add signature: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create a placeholder page for demonstration.
     */
    private function createPlaceholderPage(int $pageNumber): string
    {
        $pagesDir = public_path('pages');
        if (!is_dir($pagesDir)) {
            mkdir($pagesDir, 0755, true);
        }

        $manager = new ImageManager(new Driver());

        // Create a white page (A4 proportions at 72 DPI = 595 x 842)
        $image = $manager->create(595, 842)->fill('ffffff');

        // Add some text to indicate it's a placeholder
        // Note: For text, you'd need a font file. Using a simple approach here.

        $path = $pagesDir . "/page{$pageNumber}.jpg";
        $image->save($path, quality: 90);

        return $path;
    }

    /**
     * Create a PDF from a signed image using FPDF.
     */
    private function createPdfFromImage(string $imagePath, int $pageNumber): ?string
    {
        try {
            require_once base_path('vendor/setasign/fpdf/fpdf.php');
            $pdf = new \FPDF('P', 'pt', 'A4');
            $pdf->AddPage();

            // Get image dimensions
            list($imgWidth, $imgHeight) = getimagesize($imagePath);

            // A4 dimensions in points: 595.28 x 841.89
            $pageWidth = 595.28;
            $pageHeight = 841.89;

            // Scale image to fit page
            $scale = min($pageWidth / $imgWidth, $pageHeight / $imgHeight);
            $newWidth = $imgWidth * $scale;
            $newHeight = $imgHeight * $scale;

            // Center the image
            $x = ($pageWidth - $newWidth) / 2;
            $y = ($pageHeight - $newHeight) / 2;

            $pdf->Image($imagePath, $x, $y, $newWidth, $newHeight);

            $outputPath = public_path("signed/signed_page{$pageNumber}.pdf");
            $pdf->Output('F', $outputPath);

            return $outputPath;

        } catch (\Exception $e) {
            // PDF creation failed, but image was saved successfully
            return null;
        }
    }

    /**
     * Display the Nutrient viewer page.
     */
    public function showNutrientViewer()
    {
        return view('nutrient-viewer');
    }
}
