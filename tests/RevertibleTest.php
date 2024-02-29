<?php

namespace Buzdyk\Revertible\Testing;

use Buzdyk\Revertible\ActionStack;
use Buzdyk\Revertible\Testing\Actions\Todo\Reassign;
use Buzdyk\Revertible\Models\Revertible;

class RevertibleTest extends TestCase
{

    public function it_executes_actions_added_to_the_stack()
    {

    }

    public function it_assigns_same_uuid_to_executed_actions()
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

    /**
     * @test
     */
    public function it_persists_action_class_on_execute()
    {

    }

    /**
     * @test
     */
    public function it_skips_actions_when_applicable()
    {

    }
}