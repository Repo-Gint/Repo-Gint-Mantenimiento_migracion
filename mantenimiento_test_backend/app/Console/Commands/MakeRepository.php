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

        $name = str_replace('\\', '/', $name);

        $className = class_basename($name);
        $folder = dirname($name);

        $folderPath = $folder === '.' ? '' : $folder;

        $path = app_path("Repositories/{$folderPath}");
        $filePath = "{$path}/{$className}.php";

        if (File::exists($filePath)) {
            $this->error('Repository already exists!');
            return;
        }

        File::ensureDirectoryExists($path);

        $namespace = 'App\\Repositories' . ($folderPath ? '\\' . str_replace('/', '\\', $folderPath) : '');

                $stub = "<?php

        namespace {$namespace};

        class {$className}
        {
            //
        }
      ";

        File::put($filePath, $stub);

        $this->info("Repository created successfully.");
    }
}