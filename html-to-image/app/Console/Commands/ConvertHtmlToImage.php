<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertHtmlToImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:html-to-image {html=public/index.html} {output=public/image.png}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert HTML to image using wkhtmltoimage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $html = $this->argument('html');
        $output = $this->argument('output');

        // Security fix: escape shell arguments to prevent command injection
        $escapedHtml = escapeshellarg($html);
        $escapedOutput = escapeshellarg($output);

        $outputLines = [];
        $resultCode = null;

        exec("wkhtmltoimage --format png {$escapedHtml} {$escapedOutput} 2>&1", $outputLines, $resultCode);

        if ($resultCode !== 0) {
            $this->error("Failed to convert HTML to image");
            if (!empty($outputLines)) {
                $this->error(implode("\n", $outputLines));
            }
            return Command::FAILURE;
        }

        $this->info("HTML converted to image and saved to {$output}");
        return Command::SUCCESS;
    }
}
