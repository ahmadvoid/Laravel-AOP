<?php

namespace AhmadVoid\SimpleAOP\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class MakeAspect extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:aspect {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct($filesystem);
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): bool|int
    {
        $name = $this->getNameInput();

        $path = $this->getPath($name);

        if ($this->files->exists($path)) {
            $this->error('File already exists!');
            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info('File created successfully.');

        return true;
    }

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/aspect.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Aspects';
    }

    protected function buildClass($name): array|string
    {
        $stub = parent::buildClass($name);
        return str_replace('AspectName', $this->argument('name'), $stub);
    }

    protected function getPath($name): string
    {
        return base_path('app/Aspects/' . $name . '.php');
    }

    protected function getNameInput(): array|string|null
    {
        return $this->argument('name');
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
}
