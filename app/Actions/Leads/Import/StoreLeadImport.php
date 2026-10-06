<?php

namespace App\Actions\Leads\Import;

use App\Actions\Leads\CreateLead;
use Illuminate\Validation\ValidationException;
use Throwable;

class StoreLeadImport
{
    public function __construct(
        private ReviewLeadImport $reviewLeadImport,
        private CreateLead $createLead,
    ) {
    }

    /**
     * Persist a reviewed Lead import.
     */
    public function handle(
        array $import,
        array $matchDecisions,
        ?int $createdBy = null,
    ): array {
        /*
         * Re-run Review immediately before persistence.
         * The browser Review state is never authoritative.
         */
        $review =
            $this->reviewLeadImport->handle(
                $import
            );

        $rows =
            $review['rows'] ?? [];

        if (! is_array($rows) || $rows === []) {
            throw ValidationException::withMessages([
                'import' => [
                    'There are no Lead rows available to import.',
                ],
            ]);
        }

        /*
         * If any row now has a validation problem,
         * require the user to Review again.
         */
        if (
            ($review['issues'] ?? [])
            !== []
        ) {
            throw ValidationException::withMessages([
                'import' => [
                    'The Lead import now contains rows that need attention. Please return to Review before importing.',
                ],
            ]);
        }

        $currentMatchRows =
            collect(
                $review['matches'] ?? []
            )
                ->map(
                    static fn ($match): int =>
                        (int) (
                            $match['row_number']
                            ?? 0
                        )
                )
                ->filter(
                    static fn (int $rowNumber): bool =>
                        $rowNumber > 0
                )
                ->unique()
                ->sort()
                ->values()
                ->all();

        $decisions = [];

        foreach (
            $matchDecisions
            as $rowNumber => $decision
        ) {
            $rowNumberString =
                (string) $rowNumber;

            if (
                ! ctype_digit(
                    $rowNumberString
                )
                || (int) $rowNumberString <= 0
            ) {
                throw ValidationException::withMessages([
                    'match_decisions' => [
                        'The submitted potential match decisions are invalid. Please return to Review.',
                    ],
                ]);
            }

            $decisions[
                (int) $rowNumberString
            ] = (string) $decision;
        }

        $decisionRows =
            array_keys($decisions);

        sort($decisionRows);

        /*
         * Decisions must correspond exactly to the
         * potential matches found by the fresh Review.
         */
        if (
            $decisionRows
            !== $currentMatchRows
        ) {
            throw ValidationException::withMessages([
                'match_decisions' => [
                    'The potential matches have changed. Please return to Review and confirm your decisions again.',
                ],
            ]);
        }

        $matchLookup =
            array_fill_keys(
                $currentMatchRows,
                true
            );

        $processed = 0;
        $created = 0;
        $skipped = 0;
        $failed = 0;
        $failures = [];

        foreach ($rows as $row) {
            $rowNumber =
                (int) (
                    $row['row_number']
                    ?? 0
                );

            $payload =
                $row['payload'] ?? null;

            if (
                $rowNumber <= 0
                || ! is_array($payload)
            ) {
                continue;
            }

            /*
             * Only current potential-match rows
             * require an explicit browser decision.
             */
            if (
                isset($matchLookup[$rowNumber])
                && (
                    $decisions[$rowNumber]
                    ?? null
                ) === 'skip'
            ) {
                $processed++;
                $skipped++;

                continue;
            }

            try {
                $this->createLead->handle(
                    data: $payload,
                    createdBy: $createdBy,
                );

                $created++;
            } catch (ValidationException $exception) {
                $failed++;

                $reason =
                    collect(
                        $exception->errors()
                    )
                        ->flatten()
                        ->filter()
                        ->first()
                    ?? 'The Lead could not be created.';

                $failures[] = [
                    'row_number' =>
                        $rowNumber,

                    'display_name' =>
                        $payload[
                            'display_name'
                        ] ?? null,

                    'reason' =>
                        (string) $reason,
                ];
            } catch (Throwable $exception) {
                report($exception);

                $failed++;

                $failures[] = [
                    'row_number' =>
                        $rowNumber,

                    'display_name' =>
                        $payload[
                            'display_name'
                        ] ?? null,

                    'reason' =>
                        'The Lead could not be created.',
                ];
            }

            $processed++;
        }

        return [
            'total_count' =>
                count($rows),

            'processed_count' =>
                $processed,

            'created_count' =>
                $created,

            'skipped_count' =>
                $skipped,

            'failed_count' =>
                $failed,

            'match_count' =>
                count($currentMatchRows),

            'original_name' =>
                (string) (
                    $import['original_name']
                    ?? ''
                ),

            'failures' =>
                $failures,
        ];
    }
}
