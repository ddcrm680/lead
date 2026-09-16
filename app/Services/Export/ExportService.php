<?php

namespace App\Services\Export;

use App\Services\Export\Writers\ExportWriter;
use App\Services\Export\Writers\PdfExportWriter;
use RuntimeException;

class ExportService
{
    /**
     * Create a new export service instance.
     */
    public function __construct(
        protected ExportWriter $exportWriter,
        protected PdfExportWriter $pdfExportWriter
    ) {
    }

    /**
     * Generate and download an export.
     */
    public function download(object $export, string $format = 'csv') {
        
        $format = strtolower(trim($format));

        if ($format === 'pdf') {
            return $this->pdfExportWriter->download($export);
        }

        if (!in_array($format, ['csv', 'xlsx', 'ods'], true)) {
            throw new RuntimeException(
                "The [{$format}] export format is not supported."
            );
        }

        return $this->exportWriter->download(
            export: $export,
            format: $format,
        );
    }
}