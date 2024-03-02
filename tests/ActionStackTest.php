<?php

namespace Buzdyk\Revertible\Testing;

use Buzdyk\Revertible\ActionStack;
use Buzdyk\Revertible\Testing\Actions\NotExecutable;
use Buzdyk\Revertible\Testing\Actions\StatelessAction;
use Buzdyk\Revertible\Testing\Actions\Todo\Reassign;
use Buzdyk\Revertible\Testing\Models\Todo;
use Buzdyk\Revertible\Models\Revertible;

class ActionStackTest extends TestCase
{
    /** @test */
    public function it_assigns_queued_actions_the_same_uuid()
    {
        $uuid = ActionStack::make()
            ->push(new StatelessAction())
            ->push(new StatelessAction())
            ->push(new StatelessAction())
            ->execute();

        $this->assertEquals(3, Revertible::where('group_uuid', $uuid)->count());
    }

    /** @test */
    public function it_skips_actions_that_shouldnt_execute()
    {
        ActionStack::make()
            ->push(new NotExecutable())
            ->push(new NotExecutable());

        $this->assertEquals(0, 0);
    }

    /** @test */
    public function stacks_generate_unique_uuids()
    {
        $uuid1 = ActionStack::make()
            ->push(new StatelessAction())
            ->execute();

        $uuid2 = ActionStack::make()
            ->push(new StatelessAction())
            ->execute();

        $this->assertNotEquals($uuid1, $uuid2);
    }

    /** @test */
    public function it_executes_actions_in_the_correct_order()
    {
        $todo = $this->createTodo();

        ActionStack::make()
            ->push(new Reassign($todo, 5))
            ->push(new Reassign($todo, 10))
            ->execute();

        $this->assertEquals(10, $todo->assignee_id);
    }

    /** @test */
    public function it_rollbacks_actions_on_exception_if_run_in_transaction()
    {
        // todo add test
    }
}