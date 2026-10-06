<?php

namespace App\Actions\Leads\Import;

use Illuminate\Support\Str;

class PrepareLeadImport
{
    private const CORE_FIELDS = [
        'display_name' => [
            'label' => 'Display name',
            'aliases' => [
                'name',
                'lead name',
                'display name',
                'full name',
            ],
        ],
        'contact:phone' => [
            'label' => 'Phone',
            'aliases' => [
                'phone',
                'phone number',
                'mobile',
                'mobile number',
            ],
        ],
        'contact:whatsapp' => [
            'label' => 'WhatsApp',
            'aliases' => [
                'whatsapp',
                'whats app',
                'whatsapp number',
            ],
        ],
        'contact:email' => [
            'label' => 'Email',
            'aliases' => [
                'email',
                'email address',
                'e mail',
            ],
        ],
        'contacts' => [
            'label' => 'Contacts',
            'aliases' => [
                'contacts',
                'contact',
                'contact details',
            ],
        ],
        'city' => [
            'label' => 'City',
            'aliases' => ['city'],
        ],
        'state' => [
            'label' => 'State',
            'aliases' => [
                'state',
                'province',
            ],
        ],
        'country' => [
            'label' => 'Country',
            'aliases' => ['country'],
        ],
        'status' => [
            'label' => 'Status',
            'aliases' => [
                'status',
                'lead status',
            ],
        ],
        'pipeline_stage' => [
            'label' => 'Pipeline stage',
            'aliases' => [
                'stage',
                'pipeline stage',
            ],
        ],
        'source' => [
            'label' => 'Source',
            'aliases' => [
                'source',
                'lead source',
            ],
        ],
        'assigned_user' => [
            'label' => 'Assigned user',
            'aliases' => [
                'assigned to',
                'assigned user',
                'owner',
                'lead owner',
            ],
        ],
        'priority' => [
            'label' => 'Priority',
            'aliases' => [
                'priority',
                'lead priority',
            ],
        ],
        'tags' => [
            'label' => 'Tags',
            'aliases' => [
                'tag',
                'tags',
            ],
        ],
    ];

    private const IGNORED_HEADERS = [
        'sr no',
        'sr. no',
        'serial no',
        'serial number',
        'created at',
        'updated at',
        'last updated',
        'last updated at',
        'latest note',
    ];

    /**
     * Analyze spreadsheet headings for the Prepare step.
     *
     * This action does not read rows, validate Leads, or write data.
     */
    public function handle(
        array $headers,
        array $options,
    ): array {
        $recognizedColumns = [];
        $additionalColumns = [];
        $ignoredColumns = [];
        $columnPlan = [];

        $coreLookup = $this->buildCoreLookup();
        $ignoredLookup = $this->buildIgnoredLookup();

        $customLookup = $this->buildCustomLookup(
            $options['field_definitions'] ?? []
        );

        $usedAttributeKeys = [];

        foreach (
            $options['field_definitions'] ?? []
            as $definition
        ) {
            $key = trim(
                (string) ($definition['key'] ?? '')
            );

            if ($key !== '') {
                $usedAttributeKeys[$key] = true;
            }
        }

        foreach ($headers as $rawHeader) {
            $header = trim((string) $rawHeader);

            if ($header === '') {
                continue;
            }

            $normalizedHeader =
                $this->normalizeHeader($header);

            if (isset($ignoredLookup[$normalizedHeader])) {
                $ignoredColumns[] = [
                    'header' => $header,
                ];

                $columnPlan[$header] = [
                    'type' => 'ignore',
                ];

                continue;
            }

            $core =
                $coreLookup[$normalizedHeader]
                ?? null;

            if ($core !== null) {
                $recognizedColumns[] = [
                    'header' => $header,
                    'label' => $core['label'],
                    'kind' => 'core',
                ];

                $columnPlan[$header] = [
                    'type' => 'core',
                    'target' => $core['target'],
                ];

                continue;
            }

            $customMatches =
                $customLookup[$normalizedHeader]
                ?? [];

            if (count($customMatches) === 1) {
                $custom = $customMatches[0];

                $recognizedColumns[] = [
                    'header' => $header,
                    'label' => $custom['name'],
                    'kind' => 'custom',
                ];

                $columnPlan[$header] = [
                    'type' => 'custom',
                    'key' => $custom['key'],
                ];

                continue;
            }

            $attributeKey =
                $this->makeAttributeKey(
                    header: $header,
                    usedKeys: $usedAttributeKeys,
                );

            $usedAttributeKeys[$attributeKey] = true;

            $additionalColumns[] = [
                'header' => $header,
                'key' => $attributeKey,
            ];

            $columnPlan[$header] = [
                'type' => 'additional',
                'key' => $attributeKey,
            ];
        }

        return [
            'recognized_columns' =>
                $recognizedColumns,

            'additional_columns' =>
                $additionalColumns,

            'ignored_columns' =>
                $ignoredColumns,

            'column_plan' =>
                $columnPlan,
        ];
    }

    private function buildCoreLookup(): array
    {
        $lookup = [];

        foreach (
            self::CORE_FIELDS
            as $target => $field
        ) {
            foreach ($field['aliases'] as $alias) {
                $normalized =
                    $this->normalizeHeader($alias);

                if ($normalized === '') {
                    continue;
                }

                $lookup[$normalized] = [
                    'target' => $target,
                    'label' => $field['label'],
                ];
            }
        }

        return $lookup;
    }

    private function buildIgnoredLookup(): array
    {
        $lookup = [];

        foreach (
            self::IGNORED_HEADERS
            as $header
        ) {
            $normalized =
                $this->normalizeHeader($header);

            if ($normalized !== '') {
                $lookup[$normalized] = true;
            }
        }

        return $lookup;
    }

    private function buildCustomLookup(
        array $fieldDefinitions,
    ): array {
        $lookup = [];

        foreach (
            $fieldDefinitions
            as $definition
        ) {
            $key = trim(
                (string) ($definition['key'] ?? '')
            );

            $name = trim(
                (string) ($definition['name'] ?? '')
            );

            if ($key === '' || $name === '') {
                continue;
            }

            foreach ([$name, $key] as $alias) {
                $normalized =
                    $this->normalizeHeader($alias);

                if ($normalized === '') {
                    continue;
                }

                $lookup[$normalized] ??= [];

                $lookup[$normalized][$key] = [
                    'key' => $key,
                    'name' => $name,
                ];
            }
        }

        return array_map(
            static fn (array $matches): array =>
                array_values($matches),
            $lookup,
        );
    }

    private function normalizeHeader(
        mixed $value,
    ): string {
        return Str::of((string) $value)
            ->lower()
            ->replaceMatches('/[_-]+/u', ' ')
            ->replaceMatches(
                '/[^\p{L}\p{N}]+/u',
                ' '
            )
            ->replaceMatches('/\s+/u', ' ')
            ->trim()
            ->toString();
    }

    private function makeAttributeKey(
        string $header,
        array $usedKeys,
    ): string {
        $base = Str::of(
            Str::ascii($header)
        )
            ->snake()
            ->lower()
            ->replaceMatches(
                '/[^a-z0-9_]+/',
                '_'
            )
            ->trim('_')
            ->limit(100, '')
            ->toString();

        if ($base === '') {
            $base = 'additional_field';
        }

        if (preg_match('/^\d/', $base)) {
            $base =
                "field_{$base}";
        }

        $key = $base;
        $suffix = 2;

        while (isset($usedKeys[$key])) {
            $suffixText =
                "_{$suffix}";

            $key = Str::limit(
                $base,
                100 - strlen($suffixText),
                ''
            ) . $suffixText;

            $suffix++;
        }

        return $key;
    }
}
