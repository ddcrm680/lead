<?php

namespace App\Actions\Leads\Import;

use DateTimeInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Rap2hpoutre\FastExcel\FastExcel;
use RuntimeException;
use Throwable;

class ParseLeadImport
{
    private const SAMPLE_LIMIT = 5;

    public function handle(
        UploadedFile $file,
        int $userId,
    ): array {
        $token = (string) Str::uuid();

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $directory = "lead-imports/{$userId}";
        $filename = "{$token}.{$extension}";

        $storedPath = Storage::disk('local')->putFileAs(
            $directory,
            $file,
            $filename,
        );

        if (! $storedPath) {
            throw new RuntimeException(
                'Unable to store the Lead import file.'
            );
        }

        $absolutePath = Storage::disk('local')->path(
            $storedPath
        );

        $headers = [];
        $samples = [];
        $rowCount = 0;

        try {
            (new FastExcel())->import(
                $absolutePath,
                function ($row) use (
                    &$headers,
                    &$samples,
                    &$rowCount,
                ) {
                    if (! is_array($row) || ! $this->rowHasData($row)) {
                        return null;
                    }

                    if ($headers === []) {
                        $headers = array_map(
                            static fn ($header): string => (string) $header,
                            array_keys($row),
                        );
                    }

                    $rowCount++;

                    if (count($samples) < self::SAMPLE_LIMIT) {
                        $samples[] = $this->normalizeSampleRow(
                            $row
                        );
                    }

                    return null;
                }
            );
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storedPath);

            report($exception);

            throw ValidationException::withMessages([
                'file' => [
                    'The spreadsheet could not be read. Please check the file and try again.',
                ],
            ]);
        }

        if ($rowCount === 0 || $headers === []) {
            Storage::disk('local')->delete($storedPath);

            throw ValidationException::withMessages([
                'file' => [
                    'The spreadsheet does not contain any lead rows.',
                ],
            ]);
        }

        if (
            collect($headers)->contains(
                static fn (string $header): bool => trim($header) === ''
            )
        ) {
            Storage::disk('local')->delete($storedPath);

            throw ValidationException::withMessages([
                'file' => [
                    'Every spreadsheet column must have a heading.',
                ],
            ]);
        }

        return [
            'token' => $token,
            'path' => $storedPath,

            'original_name' => basename(
                $file->getClientOriginalName()
            ),

            'extension' => $extension,
            'size' => (int) $file->getSize(),

            'row_count' => $rowCount,
            'column_count' => count($headers),

            'headers' => $headers,
            'samples' => $samples,
        ];
    }

    private function rowHasData(array $row): bool
    {
        foreach ($row as $value) {
            if ($value === null) {
                continue;
            }

            if (
                is_string($value)
                && trim($value) === ''
            ) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function normalizeSampleRow(array $row): array
    {
        return collect($row)
            ->mapWithKeys(
                fn ($value, $key): array => [
                    (string) $key => $this->normalizeSampleValue(
                        $value
                    ),
                ]
            )
            ->all();
    }

    private function normalizeSampleValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }
}
