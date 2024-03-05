<?php

namespace Buzdyk\Revertible;

use Illuminate\Support\Collection;
use Buzdyk\Revertible\ActionStack;

class FacadeAccessor {

    public function actionStack(bool $runInTransaction): ActionStack
    {
        return ActionStack::make($runInTransaction);
    }

    public function revertStack(string $uuid, bool $runInTransaction = false): RevertStack
    {
        return RevertStack::make($uuid, $runInTransaction);
    }

}