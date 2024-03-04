<?php

namespace Buzdyk\Revertible\Testing\Actions;

use Buzdyk\Revertible\BaseAction;

class ThrowExceptionOnRevert extends BaseAction
{
    public function __construct() {}

    public function execute(): void
    {

    }

    public function revert(): void
    {
        throw new \Exception('message');
    }
}