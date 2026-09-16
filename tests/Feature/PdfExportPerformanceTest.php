<?php

use App\Services\Export\Writers\PdfExportWriter;
use Faker\Factory as FakerFactory;
use Spatie\LaravelPdf\PdfBuilder;

test('pdf export can render 2000 realistic records', function () {
    $faker = FakerFactory::create();

    $models = collect(
        array_fill(0, 2000, new stdClass())
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

    $export = new class($query, $faker)
    {
        public function __construct(
            protected $query,
            protected $faker
        ) {
        }

        public function query()
        {
            return $this->query;
        }

        public function row(object $model): array
        {
            return [
                'Name' => $this->faker->name(),
                'Email' => $this->faker->safeEmail(),
                'Phone' => $this->faker->phoneNumber(),
                'Address' => $this->faker->address(),
                'Role' => $this->faker->randomElement([
                    'Administrator',
                    'Manager',
                    'Agent',
                ]),
                'Status' => $this->faker->randomElement([
                    'ACTIVE',
                    'INACTIVE',
                ]),
                'Created At' => now()
                    ->subDays($this->faker->numberBetween(1, 365))
                    ->format('Y-m-d H:i:s'),
                'Last Updated' => now()
                    ->subDays($this->faker->numberBetween(0, 30))
                    ->format('Y-m-d H:i:s'),
            ];
        }

        public function title(): string
        {
            return 'Users Performance Test';
        }

        public function filename(string $format = 'pdf'): string
        {
            return "users-performance-test.{$format}";
        }
    };

    $startTime = microtime(true);

    $result = (new PdfExportWriter)->download($export);

    $duration = microtime(true) - $startTime;
    $peakMemory = memory_get_peak_usage(true) / 1024 / 1024;

    dump([
        'records' => 2000,
        'time_seconds' => round($duration, 2),
        'peak_memory_mb' => round($peakMemory, 2),
    ]);

    expect($result)->toBeInstanceOf(PdfBuilder::class);
});