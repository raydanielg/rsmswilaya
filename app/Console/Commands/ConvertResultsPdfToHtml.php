<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConvertResultsPdfToHtml extends Command
{
    protected $signature = 'convert:results-pdf-to-html {--limit=100}';

    protected $description = 'Convert local results PDFs to HTML using pdftohtml and store html_path in result_documents';

    public function handle(): int
    {
        $limit = (int) $this->option('limit') ?: 100;

        $this->info('Starting PDF → HTML conversion for result_documents...');

        $rows = DB::table('result_documents')
            ->whereNull('html_path')
            ->whereNotNull('file_url')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            $this->info('No rows to process.');
            return Command::SUCCESS;
        }

        $processed = 0;

        foreach ($rows as $row) {
            try {
                $fileUrl = (string) $row->file_url;

                // Skip external URLs
                if (preg_match('#^https?://#i', $fileUrl)) {
                    $this->line("Skipping external URL (id={$row->id}): {$fileUrl}");
                    continue;
                }

                // Normalise path relative to public disk
                $relative = preg_replace('#^/?storage/#', '', ltrim($fileUrl, '/'));

                $disk = Storage::disk('public');
                if (! $disk->exists($relative)) {
                    $this->warn("File not found in storage (id={$row->id}): {$relative}");
                    continue;
                }

                $sourcePath = $disk->path($relative);

                // Build output storage path
                $baseName   = pathinfo($relative, PATHINFO_FILENAME);
                $outDir     = 'results-html/' . dirname($relative);
                $outDir     = rtrim($outDir, '/.');
                $outStorage = $outDir . '/' . $baseName . '.html';

                $disk->makeDirectory($outDir);
                $targetPath = $disk->path($outStorage);

                // Call pdftohtml (must be installed on the system PATH)
                $cmd = sprintf(
                    'pdftohtml -s -q %s %s',
                    escapeshellarg($sourcePath),
                    escapeshellarg($targetPath)
                );

                $this->line("Converting (id={$row->id}): {$sourcePath} -> {$targetPath}");

                $output = [];
                $exitCode = 0;
                exec($cmd . ' 2>&1', $output, $exitCode);

                if ($exitCode !== 0) {
                    $this->error("pdftohtml failed (id={$row->id}), exit={$exitCode}: " . implode(' | ', $output));
                    continue;
                }

                DB::table('result_documents')
                    ->where('id', $row->id)
                    ->update([
                        'html_path' => $outStorage,
                        'updated_at' => now(),
                    ]);

                $processed++;
            } catch (\Throwable $e) {
                $this->error("Error processing id={$row->id}: " . $e->getMessage());
            }
        }

        $this->info("Done. Processed {$processed} row(s).");

        return Command::SUCCESS;
    }
}
