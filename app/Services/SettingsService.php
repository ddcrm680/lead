<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SettingsService
{

    public function __construct( private FileUploadService $fileUploadService)
    {
        //
    }



    /**
     * Update settings for a group.
     */
    public function update(string $group, array $settings): void
    {
        DB::transaction(function () use ($group, $settings) {

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    [
                        'setting_group' => $group,
                        'key' => $key,
                    ],
                    [
                        'value' => $this->normalizeValue($value),
                        'type' => $this->resolveType($value),
                    ]
                );
            }
        });
    }

    /**
     * Update a file-based setting.
     */
    public function updateFile(
        string $group,
        string $key,
        UploadedFile $file,
        string $directory,
        string $prefix = 'file'
    ): void {
        $setting = Setting::where([
            'setting_group' => $group,
            'key' => $key,
        ])->first();

        $oldPath = $setting?->value;

        $relativePath = $this->fileUploadService->store(
            $file,
            $directory,
            $prefix
        );

        try {
            Setting::updateOrCreate(
                [
                    'setting_group' => $group,
                    'key' => $key,
                ],
                [
                    'value' => $relativePath,
                    'type' => 'file',
                ]
            );
        } catch (\Throwable $exception) {
            $this->fileUploadService->delete($relativePath);

            throw $exception;
        }

        if ($oldPath) {
            $this->fileUploadService->delete($oldPath);
        }
    }

    /**
     * Get all settings for a group.
     */
    public function getGroup(string $group): array
    {
        return Setting::where('setting_group', $group)
            ->get()
            ->mapWithKeys(function (Setting $setting) {
                return [
                    $setting->key => $this->castValue(
                        $setting->value,
                        $setting->type
                    ),
                ];
            })
            ->all();
    }

    /**
     * Normalize a setting value before storage.
     */
    private function normalizeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    /**
     * Resolve the stored value type.
     */
    private function resolveType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'json',
            default => 'string',
        };
    }

    /**
     * Cast a stored setting value.
     */
    private function castValue(
        ?string $value,
        string $type
    ): mixed {
        return match ($type) {
            'boolean' => $value === '1',
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value ?? '[]', true),
            default => $value,
        };
    }
}