<?php

namespace App\Actions\Leads\Import;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StartLeadImport
{
    public function __construct(
        private ParseLeadImport $parseLeadImport,
        private PrepareLeadImport $prepareLeadImport,
        private GetLeadImportOptions $getLeadImportOptions,
    ) {
    }

    /**
     * Parse and prepare a Lead import.
     *
     * No Lead records are created here.
     */
    public function handle(
        UploadedFile $file,
        int $userId,
        ?string $previousPath = null,
    ): array {
        $parsed = $this->parseLeadImport->handle(
            file: $file,
            userId: $userId,
        );

        try {
            $options =
                $this->getLeadImportOptions->handle();

            $prepared =
                $this->prepareLeadImport->handle(
                    headers: $parsed['headers'],
                    options: $options,
                );
        } catch (Throwable $exception) {
            /*
             * ParseLeadImport already stored the new file.
             * If preparation fails, remove only that new file.
             */
            Storage::disk('local')->delete(
                $parsed['path']
            );

            throw $exception;
        }

        /*
         * Delete the previous temporary import only after
         * the replacement parsed and prepared successfully.
         */
        if (
            $previousPath
            && $previousPath !== $parsed['path']
        ) {
            Storage::disk('local')->delete(
                $previousPath
            );
        }

        return [
            /*
             * Private server-side import state.
             */
            'session' => [
                'token' =>
                    $parsed['token'],

                'path' =>
                    $parsed['path'],

                'original_name' =>
                    $parsed['original_name'],

                'extension' =>
                    $parsed['extension'],

                'size' =>
                    $parsed['size'],

                'row_count' =>
                    $parsed['row_count'],

                'column_count' =>
                    $parsed['column_count'],

                'headers' =>
                    $parsed['headers'],

                'column_plan' =>
                    $prepared['column_plan'],
            ],

            /*
             * Data needed to render the Prepare Blade.
             */
            'view_data' => [
                'importMeta' => [
                    'original_name' =>
                        $parsed['original_name'],

                    'extension' =>
                        $parsed['extension'],

                    'size' =>
                        $parsed['size'],

                    'row_count' =>
                        $parsed['row_count'],

                    'column_count' =>
                        $parsed['column_count'],
                ],

                'analysis' => [
                    'recognized_columns' =>
                        $prepared['recognized_columns'],

                    'additional_columns' =>
                        $prepared['additional_columns'],

                    'ignored_columns' =>
                        $prepared['ignored_columns'],

                      'has_source_column' =>
                        collect(
                            $prepared['column_plan']
                        )->contains(
                            static fn (array $column): bool =>
                                ($column['type'] ?? null) === 'core'
                                && ($column['target'] ?? null) === 'source'
                        ),
                ],

                'options' =>
                    $options,
            ],
        ];
    }
}
