<?php

namespace Buzdyk\Revertible;

use Illuminate\Console\GeneratorCommand;

class MakeRevertibleAction extends GeneratorCommand
{
    protected $name = 'make:revertible-action';

    protected function getStub()
    {
        $relativePath = '/../action.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
            ? $customPath
            : __DIR__.$relativePath;
    }
}