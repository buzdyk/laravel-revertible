<?php

namespace Buzdyk\Revertible\Testing\Actions;

use Buzdyk\Revertible\BaseAction;
use Buzdyk\Revertible\Contracts\RevertibleUpdate;
use Buzdyk\Revertible\Models\Revertible;

class ThrowExceptionOnExecute extends BaseAction
{
    public function __construct() {}

    public function execute(): RevertibleUpdate
    {
        throw new \Exception("message");
    }

    public function revert(Revertible $revertible): void
    {
        //..
    }
}