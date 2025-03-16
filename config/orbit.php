<?php

/**
 * Возвращает массив настроек
 */
return [

    'default' => env('ORBIT_DEFAULT_DRIVER', 'md'), //Выбранный драйвер по умолчанию. По умолчанию храним в Markdown

    'drivers' => [
        'md' => \Orbit\Drivers\Markdown::class, //Драйвер markdown. Это значит  - хранение файлов формате Narkdown
        'json' => \Orbit\Drivers\Json::class, //Хранение файлов в формате Json
        'yaml' => \Orbit\Drivers\Yaml::class, //Хранение файлов в формате Yaml
    ], //Типы драйверов

    'paths' => [
        'content' => base_path('content'),
        'cache' => storage_path('framework/cache/orbit'),
    ],

];
