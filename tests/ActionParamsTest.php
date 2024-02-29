<?php

namespace Buzdyk\Revertible\Testing;

use Buzdyk\Revertible\ActionStack;
use Buzdyk\Revertible\BaseAction;
use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Contracts\RevertibleUpdate;
use Buzdyk\Revertible\ExecuteUpdate;
use Buzdyk\Revertible\Models\Revertible;
use Buzdyk\Revertible\RevertStack;
use Buzdyk\Revertible\Testing\Actions\Todo\Reassign;
use Buzdyk\Revertible\Testing\Models\Todo;

class ActionParamsTest extends TestCase {

    public function revert_stack_restores_constructor_params()
    {
        $todo = new Models\Todo; // todo make a factory for this

        $uuid = ActionStack::make()
            ->push(new Reassign($todo, 10))
            ->execute();

        $revertStack = RevertStack::make($uuid)
            ->revert();

        $this->assertEquals(1, $revertStack->count());
    }
}