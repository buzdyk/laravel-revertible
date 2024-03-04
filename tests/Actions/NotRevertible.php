<?php

namespace Buzdyk\Revertible\Testing\Actions;

use Buzdyk\Revertible\BaseAction;

class NotRevertible extends BaseAction
{
    public function __construct() {}

    public function execute(): void
    {
    }

    public function revert(): void
    {
        //..
    }

    public function shouldRevert(): bool
    {
        return false;
    }
}