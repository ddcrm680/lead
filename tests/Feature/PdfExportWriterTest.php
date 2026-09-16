<?php

use App\Services\Export\Writers\PdfExportWriter;
use RuntimeException;
use Spatie\LaravelPdf\PdfBuilder;

function makePdfTestExport(int $count): object
{
    $models = collect(
        array_fill(0, $count, new stdClass())
    );

    $query = new class($models)
    {
        public function __construct(
            protected $models
        ) {
        }

        public function limit(int $limit): self
        {
            $this->models = $this->models->take($limit);

            return $this;
        }

        public function get()
        {
            return $this->models;
        }
    };

    return new class($query)
    {
        public function __construct(
            protected $query
        ) {
        }

        public function query()
        {
            return $this->query;
        }

        public function row(object $model): array
        {
            return [
                'Name' => 'Test User',
                'Email' => 'test@example.com',
                'Phone' => '1234567890',
                'Address' => 'Test Address',
                'Role' => 'Administrator',
                'Status' => 'ACTIVE',
                'Created At' => '2026-08-24 10:00:00',
                'Last Updated' => '2026-08-24 10:00:00',
            ];
        }

        public function title(): string
        {
            return 'Users Report';
        }

        public function filename(string $format = 'pdf'): string
        {
            return "users.{$format}";
        }
    };
}

test('pdf export accepts exactly 2000 records', function () {
    $export = makePdfTestExport(2000);
    $writer = new PdfExportWriter;

    $result = $writer->download($export);

    expect($result)->toBeInstanceOf(PdfBuilder::class);
});

test('pdf export rejects more than 2000 records', function () {
    $export = makePdfTestExport(2001);
    $writer = new PdfExportWriter;

    expect(fn () => $writer->download($export))
        ->toThrow(
            RuntimeException::class,
            'PDF exports are limited to 2,000 records.'
        );
});

test('pdf export accepts records within the limit', function () {
    $export = makePdfTestExport(201);
    $writer = new PdfExportWriter;

    $result = $writer->download($export);

    expect($result)->toBeInstanceOf(PdfBuilder::class);
});