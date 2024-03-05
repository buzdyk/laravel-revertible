<?php

namespace Buzdyk\Revertible\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeRevertibleAction extends GeneratorCommand
{
    protected $name = 'make:revertible';

    protected function getStub()
    {
        $relativePath = '/../action.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
            ? $customPath
            : __DIR__.$relativePath;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '');
        return $this->laravel->basePath().'/'.str_replace('\\', '/', $name).'.php';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Revertibles';
    }

}