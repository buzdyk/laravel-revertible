<?php

namespace Buzdyk\Revertible\Testing\Actions;

use Buzdyk\Revertible\BaseAction;

class NotExecutable extends BaseAction
{
    public function __construct() {}

    public function execute(): void
    {

    }

    public function revert(): void
    {
        //..
    }

    public function shouldExecute(): bool
    {
        return false;
    }
}