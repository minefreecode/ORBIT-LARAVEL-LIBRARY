<?php

namespace Orbit\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Orbit\Concerns\Orbital;
use ReflectionClass;

class CacheCommand extends Command
{
    protected $name = 'orbit:cache';

    protected $description = 'Cache all Orbit models.';

    public function handle()
    {
        $models = $this->findOrbitModels();

        if ($models->isEmpty()) {
            $this->warn('Could not find any Orbit models.');

            return 0;
        }

        $models->each(function (string $model): void {
            (new $model())->migrate();
        });

        $this->info('Cached the following Orbit models:');
        $this->newLine();
        $this->line($models->map(fn ($model) => "• <info>{$model}</info>"));

        return 0;
    }

    protected function findOrbitModels(): Collection
    {
        $laravel = $this->getLaravel();

        //Изем коллекцию моделей для работы с ними
        return collect(File::allFiles($laravel->path()))//Получить все файлы
            //Цикл по всем файлам
            ->map(function ($item) use ($laravel) {
                // Convert file path to namespace
                $path = $item->getRelativePathName();
                $ns = $laravel->getNamespace();
                $class = sprintf(
                    '\%s%s',
                    str_ends_with($ns, "\\") ? $ns : $ns."\\",
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );

                return $class;
            })
            //Фильтровать классы
            ->filter(function ($class) {
                if (! class_exists($class)) {
                    return false;
                }
                //Объект рефлексий
                $reflection = new ReflectionClass($class);

                //Если класс относится к типу моделей
                return $reflection->isSubclassOf(Model::class) &&
                    ! $reflection->isAbstract() &&
                    isset(class_uses_recursive($class)[Orbital::class]);
            });
    }
}
