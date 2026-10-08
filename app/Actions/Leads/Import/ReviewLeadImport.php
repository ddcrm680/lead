<?php

namespace App\Actions\Leads\Import;

use App\Models\LeadContact;
use App\Services\ContactNormalizer;
use App\Services\LeadPayloadValidator;
use DateTimeInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Rap2hpoutre\FastExcel\FastExcel;
use Throwable;

class ReviewLeadImport
{
    private const SAMPLE_LIMIT = 5;

    public function __construct(
        private GetLeadImportOptions $getLeadImportOptions,
        private LeadPayloadValidator $leadPayloadValidator,
        private ContactNormalizer $contactNormalizer,
    ) {
    }

    /**
     * Build a read-only Review summary.
     *
     * No Lead records are created here.
     */
    public function handle(array $import): array
    {
        $path = trim(
            (string) ($import['path'] ?? '')
        );

        $columnPlan =
            $import['column_plan'] ?? null;

        $defaults =
            $import['defaults'] ?? null;

        if (
            $path === ''
            || ! is_array($columnPlan)
            || $columnPlan === []
            || ! is_array($defaults)
        ) {
            throw ValidationException::withMessages([
                'file' => [
                    'The Lead import session is incomplete. Please upload the file again.',
                ],
            ]);
        }

        if (! Storage::disk('local')->exists($path)) {
            throw ValidationException::withMessages([
                'file' => [
                    'The Lead import file is no longer available. Please upload it again.',
                ],
            ]);
        }

        $options =
            $this->getLeadImportOptions->handle();

        $lookups = [
            'status' =>
                $this->buildOptionLookup(
                    $options['statuses'] ?? []
                ),

            'source' =>
                $this->buildOptionLookup(
                    $options['sources'] ?? []
                ),

            'pipeline_stage' =>
                $this->buildPipelineStageLookup(
                    $options['pipeline_stages'] ?? []
                ),

            'assigned_user' =>
                $this->buildOptionLookup(
                    $options['users'] ?? []
                ),

            'priority' =>
                $this->buildOptionLookup(
                    $options['priorities'] ?? []
                ),
        ];

        $resolvedDefaults =
            $this->resolveDefaults(
                $defaults,
                $lookups,
            );

        $additionalKeys =
            $this->additionalAttributeKeys(
                $columnPlan
            );

        $absolutePath =
            Storage::disk('local')->path($path);

        $rows = [];
        $rowNumber = 1;

        try {
            (new FastExcel())->import(
                $absolutePath,
                function ($row) use (
                    &$rows,
                    &$rowNumber,
                    $columnPlan,
                    $resolvedDefaults,
                    $lookups,
                    $additionalKeys,
                ) {
                    $rowNumber++;

                    if (
                        ! is_array($row)
                        || ! $this->rowHasData($row)
                    ) {
                        return null;
                    }

                    [
                        $payload,
                        $errors,
                    ] = $this->buildPayload(
                        row: $row,
                        columnPlan: $columnPlan,
                        defaults: $resolvedDefaults,
                        lookups: $lookups,
                    );

                    $validator = Validator::make(
                        $payload,
                        $this->leadPayloadValidator
                            ->rules(),
                    );

                    $this->leadPayloadValidator
                        ->after(
                            validator: $validator,
                            data: $payload,
                            allowedAttributeKeys:
                                $additionalKeys,
                        );

                    if ($validator->fails()) {
                        $errors = array_merge(
                            $errors,
                            $validator
                                ->errors()
                                ->all(),
                        );
                    }

                    $normalizedContacts = [];

                    if ($errors === []) {
                        [
                            $normalizedContacts,
                            $contactErrors,
                        ] = $this->normalizeContacts(
                            $payload['contacts'] ?? []
                        );

                        $errors = array_merge(
                            $errors,
                            $contactErrors,
                        );
                    }

                    $rows[] = [
                        'row_number' =>
                            $rowNumber,

                        'payload' =>
                            $payload,

                        'errors' =>
                            array_values(
                                array_unique(
                                    $errors
                                )
                            ),

                        'normalized_contacts' =>
                            $normalizedContacts,
                    ];

                    return null;
                }
            );
        } catch (Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'file' => [
                    'The spreadsheet could not be reviewed. Please check the file and try again.',
                ],
            ]);
        }

        return $this->buildReviewResult(
            $rows
        );
    }

    private function buildPayload(
        array $row,
        array $columnPlan,
        array $defaults,
        array $lookups,
    ): array {
        $payload = [
            'display_name' => null,

            'source_id' =>
                $defaults['source_id'],

            'status_id' =>
                $defaults['status_id'],

            'pipeline_stage_id' =>
                null,

            'assigned_user_id' =>
                $defaults['assigned_user_id'],

            'priority' =>
                $defaults['priority'],

            'city' => null,
            'state' => null,
            'country' => null,

            'attributes' => [],
            'contacts' => [],
            'tags' => [],
        ];

        $errors = [];

        foreach (
            $columnPlan
            as $header => $plan
        ) {
            if (! is_array($plan)) {
                continue;
            }

            $type =
                (string) ($plan['type'] ?? '');

            if ($type === 'ignore') {
                continue;
            }

            $value =
                $this->normalizeCellValue(
                    $row[$header] ?? null
                );

            if ($type === 'custom') {
                $key = trim(
                    (string) (
                        $plan['key'] ?? ''
                    )
                );

                if (
                    $key !== ''
                    && $value !== ''
                ) {
                    $payload['attributes'][$key] =
                        $value;
                }

                continue;
            }

            if ($type === 'additional') {
                $key = trim(
                    (string) (
                        $plan['key'] ?? ''
                    )
                );

                if (
                    $key !== ''
                    && $value !== ''
                ) {
                    $payload['attributes'][$key] =
                        $value;
                }

                continue;
            }

            if ($type !== 'core') {
                continue;
            }

            $this->applyCoreValue(
                payload: $payload,
                errors: $errors,
                target: (string) (
                    $plan['target'] ?? ''
                ),
                value: $value,
                lookups: $lookups,
            );
        }

        return [
            $payload,
            array_values(
                array_unique($errors)
            ),
        ];
    }

    private function applyCoreValue(
        array &$payload,
        array &$errors,
        string $target,
        string $value,
        array $lookups,
    ): void {
        if ($value === '') {
            return;
        }

        switch ($target) {
            case 'display_name':
            case 'city':
            case 'state':
            case 'country':
                $payload[$target] =
                    $value;

                return;

            case 'contact:phone':
                $this->addContact(
                    $payload,
                    'phone',
                    $value,
                );

                return;

            case 'contact:whatsapp':
                $this->addContact(
                    $payload,
                    'whatsapp',
                    $value,
                );

                return;

            case 'contact:email':
                $this->addContact(
                    $payload,
                    'email',
                    $value,
                );

                return;

            case 'contacts':
                [
                    $contacts,
                    $contactErrors,
                ] = $this->parseCombinedContacts(
                    $value
                );

                foreach ($contacts as $contact) {
                    $payload['contacts'][] =
                        $contact;
                }

                $errors = array_merge(
                    $errors,
                    $contactErrors,
                );

                return;

            case 'status':
                $this->applyResolvedOption(
                    payload: $payload,
                    errors: $errors,
                    payloadKey: 'status_id',
                    rawValue: $value,
                    lookup: $lookups['status'],
                    label: 'Status',
                );

                return;

            case 'source':
                $this->applyResolvedOption(
                    payload: $payload,
                    errors: $errors,
                    payloadKey: 'source_id',
                    rawValue: $value,
                    lookup: $lookups['source'],
                    label: 'Source',
                );

                return;

            case 'pipeline_stage':
                $this->applyResolvedOption(
                    payload: $payload,
                    errors: $errors,
                    payloadKey:
                        'pipeline_stage_id',
                    rawValue: $value,
                    lookup:
                        $lookups['pipeline_stage'],
                    label: 'Pipeline stage',
                );

                return;

            case 'assigned_user':
                if (
                    $this->normalizeLookupValue($value)
                    === 'unassigned'
                ) {
                    $payload['assigned_user_id'] = null;

                    return;
                }

                $this->applyResolvedOption(
                    payload: $payload,
                    errors: $errors,
                    payloadKey:
                        'assigned_user_id',
                    rawValue: $value,
                    lookup:
                        $lookups['assigned_user'],
                    label: 'Assigned user',
                );

                return;

            case 'priority':
                $this->applyResolvedOption(
                    payload: $payload,
                    errors: $errors,
                    payloadKey: 'priority',
                    rawValue: $value,
                    lookup:
                        $lookups['priority'],
                    label: 'Priority',
                );

                return;

            case 'tags':
                $payload['tags'] =
                    array_values(
                        array_unique(
                            array_merge(
                                $payload['tags'],
                                $this->splitTags(
                                    $value
                                ),
                            )
                        )
                    );

                return;
        }
    }

    private function applyResolvedOption(
        array &$payload,
        array &$errors,
        string $payloadKey,
        string $rawValue,
        array $lookup,
        string $label,
    ): void {
        [
            $resolved,
            $error,
        ] = $this->resolveOption(
            $rawValue,
            $lookup,
            $label,
        );

        if ($error !== null) {
            $errors[] = $error;

            return;
        }

        $payload[$payloadKey] =
            $resolved;
    }

    private function resolveDefaults(
        array $defaults,
        array $lookups,
    ): array {
        return [
            'status_id' =>
                $this->resolveDefaultId(
                    $defaults['status_id']
                        ?? null,
                    $lookups['status'],
                    'default status',
                    required: true,
                ),

            'source_id' =>
                $this->resolveDefaultId(
                    $defaults['source_id']
                        ?? null,
                    $lookups['source'],
                    'default source',
                ),

            'assigned_user_id' =>
                $this->resolveDefaultId(
                    $defaults[
                        'assigned_user_id'
                    ] ?? null,
                    $lookups['assigned_user'],
                    'default assigned user',
                ),

            'priority' =>
                $this->resolveDefaultId(
                    $defaults['priority']
                        ?? null,
                    $lookups['priority'],
                    'default priority',
                    required: true,
                ),
        ];
    }

    private function resolveDefaultId(
        mixed $value,
        array $lookup,
        string $label,
        bool $required = false,
    ): ?int {
        $value = trim(
            (string) ($value ?? '')
        );

        if ($value === '') {
            if ($required) {
                throw ValidationException::withMessages([
                    'defaults' => [
                        ucfirst($label)
                        . ' is required.',
                    ],
                ]);
            }

            return null;
        }

        if (
            ! isset(
                $lookup['by_value'][$value]
            )
        ) {
            throw ValidationException::withMessages([
                'defaults' => [
                    ucfirst($label)
                    . ' is no longer available. Please return to Prepare and select it again.',
                ],
            ]);
        }

        return (int) $lookup[
            'by_value'
        ][$value]['value'];
    }

    private function buildOptionLookup(
        array $items,
    ): array {
        $lookup = [
            'by_value' => [],
            'by_label' => [],
        ];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $value = trim(
                (string) (
                    $item['value'] ?? ''
                )
            );

            $label = trim(
                (string) (
                    $item['label'] ?? ''
                )
            );

            if ($value === '') {
                continue;
            }

            $lookup['by_value'][$value] =
                $item;

            if ($label === '') {
                continue;
            }

            $normalized =
                $this->normalizeLookupValue(
                    $label
                );

            $lookup['by_label'][$normalized]
                ??= [];

            $lookup['by_label'][$normalized][] =
                $item;
        }

        return $lookup;
    }

    private function buildPipelineStageLookup(
        array $items,
    ): array {
        $lookup = [
            'by_value' => [],
            'by_label' => [],
        ];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $value = trim(
                (string) (
                    $item['value'] ?? ''
                )
            );

            $label = trim(
                (string) (
                    $item['label'] ?? ''
                )
            );

            $pipeline = trim(
                (string) (
                    $item['pipeline_name']
                    ?? ''
                )
            );

            if ($value === '') {
                continue;
            }

            $lookup['by_value'][$value] =
                $item;

            $labels = [$label];

            if (
                $pipeline !== ''
                && $label !== ''
            ) {
                $labels[] =
                    "{$pipeline} / {$label}";

                $labels[] =
                    "{$pipeline} - {$label}";
            }

            foreach (
                array_unique($labels)
                as $candidate
            ) {
                if ($candidate === '') {
                    continue;
                }

                $normalized =
                    $this->normalizeLookupValue(
                        $candidate
                    );

                $lookup['by_label'][$normalized]
                    ??= [];

                $lookup['by_label'][$normalized][] =
                    $item;
            }
        }

        return $lookup;
    }

    private function resolveOption(
        string $rawValue,
        array $lookup,
        string $label,
    ): array {
        $value = trim($rawValue);

        if (
            isset(
                $lookup['by_value'][$value]
            )
        ) {
            return [
                (int) $lookup[
                    'by_value'
                ][$value]['value'],
                null,
            ];
        }

        $normalized =
            $this->normalizeLookupValue(
                $value
            );

        $matches =
            $lookup['by_label'][$normalized]
            ?? [];

        if (count($matches) === 1) {
            return [
                (int) $matches[0]['value'],
                null,
            ];
        }

        if ($matches === []) {
            return [
                null,
                "{$label} \"{$value}\" does not match an active CMS option.",
            ];
        }

        return [
            null,
            "{$label} \"{$value}\" matches more than one active CMS option.",
        ];
    }

    private function normalizeLookupValue(
        string $value,
    ): string {
        return Str::of($value)
            ->lower()
            ->replaceMatches(
                '/\s+/u',
                ' '
            )
            ->trim()
            ->toString();
    }

    private function additionalAttributeKeys(
        array $columnPlan,
    ): array {
        $keys = [];

        foreach ($columnPlan as $plan) {
            if (
                ! is_array($plan)
                || ($plan['type'] ?? null)
                    !== 'additional'
            ) {
                continue;
            }

            $key = trim(
                (string) (
                    $plan['key'] ?? ''
                )
            );

            if ($key !== '') {
                $keys[] = $key;
            }
        }

        return array_values(
            array_unique($keys)
        );
    }

    private function addContact(
        array &$payload,
        string $type,
        string $value,
    ): void {
        $payload['contacts'][] = [
            'type' => $type,
            'value' => $value,
        ];
    }

    private function parseCombinedContacts(
        string $value,
    ): array {
        $contacts = [];
        $errors = [];

        $parts = preg_split(
            '/\s*(?:,|;|\r\n|\n|\r)\s*/u',
            trim($value),
            -1,
            PREG_SPLIT_NO_EMPTY,
        ) ?: [];

        foreach ($parts as $part) {
            if (
                ! preg_match(
                    '/^(email|phone|whats\s*app)\s*:\s*(.+)$/iu',
                    trim($part),
                    $matches,
                )
            ) {
                $errors[] =
                    "Contact value \"{$part}\" could not be understood. Use Email:, Phone:, or WhatsApp:.";

                continue;
            }

            $type = strtolower(
                preg_replace(
                    '/\s+/u',
                    '',
                    $matches[1],
                )
            );

            $contactType = match ($type) {
                'email' =>
                    'email',

                'whatsapp' =>
                    'whatsapp',

                default =>
                    'phone',
            };

            $contactValue =
                trim($matches[2]);

            if ($contactValue === '') {
                continue;
            }

            $contacts[] = [
                'type' =>
                    $contactType,

                'value' =>
                    $contactValue,
            ];
        }

        return [
            $contacts,
            $errors,
        ];
    }

    private function splitTags(
        string $value,
    ): array {
        $tags = preg_split(
            '/[\r\n,;]+/u',
            $value,
            -1,
            PREG_SPLIT_NO_EMPTY,
        ) ?: [];

        return collect($tags)
            ->map(
                static fn ($tag): string =>
                    trim((string) $tag)
            )
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeContacts(
        array $contacts,
    ): array {
        $normalized = [];
        $errors = [];
        $seen = [];

        foreach ($contacts as $contact) {
            if (! is_array($contact)) {
                continue;
            }

            $type = (string) (
                $contact['type'] ?? ''
            );

            $value = trim(
                (string) (
                    $contact['value'] ?? ''
                )
            );

            if (
                $type === ''
                || $value === ''
            ) {
                continue;
            }

            $normalizedValue =
                $this->contactNormalizer
                    ->normalize(
                        $type,
                        $value,
                    );

            $pair =
                "{$type}|{$normalizedValue}";

            if (isset($seen[$pair])) {
                $errors[] =
                    "The {$type} contact \"{$value}\" appears more than once in this row.";

                continue;
            }

            $seen[$pair] = true;

            $normalized[] = [
                'type' =>
                    $type,

                'value' =>
                    $value,

                'normalized_value' =>
                    $normalizedValue,

                'pair' =>
                    $pair,
            ];
        }

        return [
            $normalized,
            $errors,
        ];
    }

    private function buildReviewResult(
            array $rows,
        ): array {
            $issues = [];
            $validRows = [];

            foreach ($rows as $row) {
                if (
                    ($row['errors'] ?? [])
                    !== []
                ) {
                    $issues[] = [
                        'row_number' =>
                            $row['row_number'],

                        'display_name' =>
                            $row['payload']
                                ['display_name']
                            ?? null,

                        'errors' =>
                            $row['errors'],
                    ];

                    continue;
                }

                $validRows[] = $row;
            }

            $existingByPair =
                $this->loadExistingContacts(
                    $validRows
                );

            /*
             * Tracks contacts belonging to uploaded rows that
             * have already been accepted as ready.
             *
             * This gives us deterministic "first valid row wins"
             * behavior for duplicates inside the same file.
             */
            $acceptedImportPairs = [];

            $matches = [];
            $sample = [];
            $readyCount = 0;

            foreach ($validRows as $row) {
                $rowMatches = [];

                foreach (
                    $row['normalized_contacts']
                    as $contact
                ) {
                    $pair =
                        $contact['pair'];

                    $sourcePair =
                        $this->sourceContactKey(
                            (int) $row['payload']['source_id'],
                            $pair,
                        );

                    /*
                     * Duplicate against an existing CRM Lead.
                     */
                    foreach (
                        $existingByPair[$sourcePair]
                            ?? []
                        as $existingContact
                    ) {
                        $lead =
                            $existingContact->lead;

                        if (! $lead) {
                            continue;
                        }

                        $key =
                            "lead:{$lead->id}:{$pair}";

                        $rowMatches[$key] = [
                            'contact_type' =>
                                $contact['type'],

                            'contact_value' =>
                                $existingContact->value,

                            'lead' => [
                                'display_name' =>
                                    $lead->display_name,

                                'public_id' =>
                                    $lead->public_id,
                            ],
                        ];
                    }

                    /*
                     * Duplicate against an earlier uploaded row
                     * that has already been accepted as ready.
                     */
                    if (
                        isset(
                            $acceptedImportPairs[
                                $sourcePair
                            ]
                        )
                    ) {
                        $otherRow =
                            $acceptedImportPairs[
                                $sourcePair
                            ];

                        $key =
                            'row:'
                            . $otherRow['row_number']
                            . ":{$pair}";

                        $rowMatches[$key] = [
                            'contact_type' =>
                                $contact['type'],

                            'contact_value' =>
                                $contact['value'],

                            'lead' => [
                                'display_name' =>
                                    'Import row '
                                    . $otherRow[
                                        'row_number'
                                    ]
                                    . ': '
                                    . (
                                        $otherRow[
                                            'display_name'
                                        ]
                                        ?: 'Lead'
                                    ),

                                'public_id' =>
                                    null,
                            ],
                        ];
                    }
                }

                /*
                 * Any matching contact makes the whole Lead row
                 * a duplicate under the current business rule.
                 */
                if ($rowMatches !== []) {
                    $matches[] = [
                        'row_number' =>
                            $row['row_number'],

                        'display_name' =>
                            $row['payload']
                                ['display_name']
                            ?? null,

                        'contacts' =>
                            $row['payload']
                                ['contacts']
                            ?? [],

                        'matches' =>
                            array_values(
                                $rowMatches
                            ),
                    ];

                    continue;
                }

                /*
                 * This row is accepted.
                 *
                 * Only accepted rows reserve their contacts so
                 * a skipped duplicate cannot block later rows
                 * using one of its other unique contacts.
                 */
                foreach (
                    $row['normalized_contacts']
                    as $contact
                ) {
                    $sourcePair =
                        $this->sourceContactKey(
                            (int) $row['payload']['source_id'],
                            $contact['pair'],
                        );

                    $acceptedImportPairs[
                        $sourcePair
                    ] = [
                        'row_number' =>
                            $row['row_number'],

                        'display_name' =>
                            $row['payload']
                                ['display_name']
                            ?? null,
                    ];
                }

                $readyCount++;

                if (
                    count($sample)
                    < self::SAMPLE_LIMIT
                ) {
                    $sample[] = [
                        'row_number' =>
                            $row['row_number'],

                        'display_name' =>
                            $row['payload']
                                ['display_name']
                            ?? null,

                        'contacts' =>
                            $row['payload']
                                ['contacts']
                            ?? [],

                        'city' =>
                            $row['payload']
                                ['city']
                            ?? null,

                        'state' =>
                            $row['payload']
                                ['state']
                            ?? null,

                        'country' =>
                            $row['payload']
                                ['country']
                            ?? null,
                    ];
                }
            }

            return [
                'total_count' =>
                    count($rows),

                'ready_count' =>
                    $readyCount,

                'issue_count' =>
                    count($issues),

                /*
                 * Kept temporarily for the current Review /
                 * Store contract. These rows will become
                 * auto-skipped duplicates in the next steps.
                 */
                'match_count' =>
                    count($matches),

                'issues' =>
                    $issues,

                'matches' =>
                    $matches,

                'sample' =>
                    $sample,

                'rows' =>
                    $rows,
            ];
        }

    private function sourceContactKey(
        int $sourceId,
        string $pair,
    ): string {
        return "{$sourceId}|{$pair}";
    }

    private function loadExistingContacts(
        array $rows,
    ): array {
        $values = [];
        $types = [];

        foreach ($rows as $row) {
            foreach (
                $row['normalized_contacts']
                as $contact
            ) {
                $values[] =
                    $contact[
                        'normalized_value'
                    ];

                $types[] =
                    $contact['type'];
            }
        }

        $values = array_values(
            array_unique($values)
        );

        $types = array_values(
            array_unique($types)
        );

        if (
            $values === []
            || $types === []
        ) {
            return [];
        }

        $byPair = [];

        foreach (
            array_chunk($values, 500)
            as $valueChunk
        ) {
            $contacts =
                LeadContact::query()
                    ->whereIn(
                        'normalized_value',
                        $valueChunk,
                    )
                    ->whereIn(
                        'type',
                        $types,
                    )
                    ->with([
                        'lead:id,public_id,display_name,source_id',
                    ])
                    ->get([
                        'id',
                        'lead_id',
                        'type',
                        'value',
                        'normalized_value',
                    ]);

            foreach ($contacts as $contact) {
                if (! $contact->lead) {
                    continue;
                }

                $pair =
                    $contact->type
                    . '|'
                    . $contact
                        ->normalized_value;

                $sourcePair =
                    $this->sourceContactKey(
                        (int) $contact->lead->source_id,
                        $pair,
                    );

                $byPair[$sourcePair] ??= [];

                $byPair[$sourcePair][] =
                    $contact;
            }
        }

        return $byPair;
    }

    private function rowHasData(
        array $row,
    ): bool {
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

    private function normalizeCellValue(
        mixed $value,
    ): string {
        if ($value === null) {
            return '';
        }

        if (
            $value
            instanceof DateTimeInterface
        ) {
            return $value->format(
                'Y-m-d H:i:s'
            );
        }

        if (is_bool($value)) {
            return $value
                ? 'Yes'
                : 'No';
        }

        if (is_scalar($value)) {
            return trim(
                (string) $value
            );
        }

        return '';
    }
}
