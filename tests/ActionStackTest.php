<?php

namespace Buzdyk\Revertible\Testing;

use Buzdyk\Revertible\ActionStack;
use Buzdyk\Revertible\Testing\Actions\Todo\Reassign;
use Buzdyk\Revertible\Testing\Models\Todo;
use Buzdyk\Revertible\Models\Revertible;

class ActionStackTest extends TestCase
{

    public function it_executes_all_actions_added_to_the_stack()
    {

    }

    public function it_assigns_uuid_to_executed_actions()
    {

    }

    /**
     * @test
     */
    public function it_persists_constructor_params_on_execute()
    {
        $todo = new Models\Todo; // @todo make a factory for this

        ActionStack::make()
            ->push(new Reassign($todo, 10))
            ->execute();

        $revertible = Revertible::first();
        $rawParams = $revertible->expandParameters();

        $this->assertArrayHasKey('todo', $rawParams);
        $this->assertArrayHasKey('assigneeId', $rawParams);
        $this->assertEquals('__eloquent__model:' . $todo::class . ':' . $todo->id, $rawParams['todo']);
        $this->assertEquals(10, $rawParams['assigneeId']);
    }

    public function it_persists_action_class_on_execute()
    {

    }

    public function it_skips_actions_that_shouldnt_execute()
    {

    }
}
