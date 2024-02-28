<?php

namespace Buzdyk\Revertible;

use Illuminate\Support\Collection;

class FacadeAccessor {

    public function actionStack(Collection $actions = new Collection()): ActionStack
    {
        return new ActionStack();
    }

    public function revertStack(string $uuid): RevertStack
    {
        return RevertStack::make($uuid);
    }

}