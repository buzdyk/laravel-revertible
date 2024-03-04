<?php

namespace Buzdyk\Revertible\Testing\Actions;

use Buzdyk\Revertible\BaseAction;

class ThrowExceptionOnExecute extends BaseAction
{
    public function __construct() {}

    public function execute(): void
    {
        throw new \Exception("message");
    }

    public function revert(): void
    {
        //..
    }
}