<?php

namespace Buzdyk\Revertible\Testing\Actions;

use Buzdyk\Revertible\ActionUpdate;
use Buzdyk\Revertible\BaseAction;
use Buzdyk\Revertible\Contracts\RevertibleUpdate;
use Buzdyk\Revertible\Models\Revertible;

class ThrowExceptionOnRevert extends BaseAction
{
    public function __construct() {}

    public function execute(): RevertibleUpdate
    {
        return new ActionUpdate();
    }

    public function revert(Revertible $revertible): void
    {
        throw new \Exception('message');
    }
}