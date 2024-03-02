<?php

namespace Buzdyk\Revertible\Testing\Actions;

use Buzdyk\Revertible\ActionUpdate;
use Buzdyk\Revertible\BaseAction;
use Buzdyk\Revertible\Contracts\RevertibleUpdate;
use Buzdyk\Revertible\Models\Revertible;

class NotRevertible extends BaseAction
{
    public function __construct() {}

    public function execute(): RevertibleUpdate
    {
        return new ActionUpdate();
    }

    public function revert(Revertible $revertible): void
    {
        //..
    }

    public function shouldRevert(Revertible $revertible): bool
    {
        return false;
    }
}