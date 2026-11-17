<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Create a new repository class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Repositories/{$name}.php");

        if (File::exists($path)) {
            $this->error('Repository already exists!');
            return;
        }

        File::ensureDirectoryExists(app_path('Repositories'));

        $stub = "<?php

namespace App\Repositories;

class {$name}
{
    //
}
";

        File::put($path, $stub);

        $this->info("Repository created successfully.");
    }
}