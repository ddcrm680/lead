<?php

namespace App\Services\Export\Writers;

use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportWriter
{
    /**
     * Generate and download an export.
     */
    public function download(
        object $export,
        string $format
    ): StreamedResponse {
        $rows = $export->query()
            ->cursor()
            ->map(fn ($model) => $export->row($model));

        return (new FastExcel($rows))
            ->download($export->filename($format));
    }
}