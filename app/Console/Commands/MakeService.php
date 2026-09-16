<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('make:service {name}')]
#[Description('Create a new application service class')]
class MakeService extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));

        if (!$name) {
            $this->components->error('Service name is required.');

            return self::FAILURE;
        }

        $directory = app_path('Services');
        $filePath = $directory . '/' . $name . '.php';

        if (file_exists($filePath)) {
            $this->components->error(
                "Service [{$name}] already exists."
            );

            return self::FAILURE;
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $contents = <<<PHP
<?php

namespace App\\Services;

class {$name}
{
    //
}

PHP;

        file_put_contents($filePath, $contents);

        $this->components->info(
            "Service [{$name}] created successfully."
        );

        return self::SUCCESS;
    }
}