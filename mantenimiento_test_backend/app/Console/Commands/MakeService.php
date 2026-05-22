<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');

        // Convertir a formato correcto
        $name = str_replace('\\', '/', $name);

        $className = class_basename($name);
        $folder = dirname($name);

        $folderPath = $folder === '.' ? '' : $folder;

        $path = app_path("Services/{$folderPath}");
        $filePath = "{$path}/{$className}.php";

        if (File::exists($filePath)) {
            $this->error('Service already exists!');
            return;
        }

        // Crear carpetas automáticamente
        File::ensureDirectoryExists($path);

        // Namespace dinámico
        $namespace = 'App\\Services' . ($folderPath ? '\\' . str_replace('/', '\\', $folderPath) : '');

        $stub = "<?php

        namespace {$namespace};

        class {$className}
        {
            //
        }
    ";

        File::put($filePath, $stub);

        $this->info("Service created successfully.");
    }
}