<?php

namespace App\Services\Export\Writers;

use RuntimeException;
use Spatie\LaravelPdf\Facades\Pdf;

class PdfExportWriter
{
    /**
     * Maximum number of records allowed in a PDF export.
     */
    protected int $maxRows = 500;

    /**
     * Generate and download a PDF export.
     */
    public function download(object $export)
    {
        $rows = $export->query()
            ->limit($this->maxRows + 1)
            ->get()
            ->map(fn ($model) => $export->row($model));

        if ($rows->count() > $this->maxRows) {
            throw new RuntimeException(
                "PDF exports are limited to {$this->maxRows} records. "
                . 'Please use CSV or Excel for larger exports.'
            );
        }

        $headings = $rows->first()
            ? array_keys($rows->first())
            : [];

        return Pdf::view('exports.pdf.table', [
            'title' => $export->title(),
            'headings' => $headings,
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ])->download(
            $export->filename('pdf')
        );
    }
}