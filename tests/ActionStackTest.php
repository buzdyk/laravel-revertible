<?php

namespace Buzdyk\Revertible\Testing;

use Buzdyk\Revertible\ActionStack;
use Buzdyk\Revertible\Testing\Actions\NotExecutable;
use Buzdyk\Revertible\Testing\Actions\StatelessAction;
use Buzdyk\Revertible\Testing\Actions\ThrowExceptionOnExecute;
use Buzdyk\Revertible\Testing\Actions\Todo\ChangeStatus;
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

    /** @test  */
    public function it_sets_executed_true_on_executed_actions()
    {
        $query = Revertible::where('executed', true);

        $this->assertEquals(0, $query->count());

        ActionStack::make()
            ->push(new StatelessAction())
            ->push(new StatelessAction())
            ->execute();

        $this->assertEquals(2, $query->count());
    }

    /** @test  */
    public function reverted_is_false_on_executed_actions()
    {
        $query = Revertible::where('reverted', false);

        $this->assertEquals(0, $query->count());

        ActionStack::make()
            ->push(new StatelessAction())
            ->push(new StatelessAction())
            ->execute();

        $this->assertEquals(2, $query->count());
    }

    /** @test */
    public function it_skips_actions_that_shouldnt_execute()
    {
        ActionStack::make()
            ->push(new NotExecutable())
            ->push(new NotExecutable())
            ->push(new StatelessAction());

        $this->assertEquals(0, Revertible::count());
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
        $todo = $this->createTodo();
        $caughtException = false;

        try {
            ActionStack::make(true)
                ->push(new Reassign($todo, 5))
                ->push(new ThrowExceptionOnExecute())
                ->push(new ChangeStatus($todo, 'accepted'))
                ->execute();
        } catch (\Exception $e) {
            $this->assertEquals('message', $e->getMessage());
            $caughtException = true;
        }

        $this->assertTrue($caughtException);

        $todo = $todo->fresh();

        $this->assertEquals(0, Revertible::count());
        $this->assertEquals(1, $todo->assignee_id);
        $this->assertEquals('new', $todo->status);
    }

    /** @test */
    public function it_doesnt_rollback_actions_if_not_run_in_transaction()
    {
        $todo = $this->createTodo();
        $caughtException = false;

        try {
            ActionStack::make(false)
                ->push(new Reassign($todo, 5))
                ->push(new ThrowExceptionOnExecute())
                ->push(new ChangeStatus($todo, 'accepted'))
                ->execute();
        } catch (\Exception $e) {
            $this->assertEquals('message', $e->getMessage());
            $caughtException = true;
        }

        $this->assertTrue($caughtException);

        $todo = $todo->fresh();

        $this->assertEquals(1, Revertible::count());
        $this->assertEquals(5, $todo->assignee_id);
        $this->assertEquals($todo->status, $todo->status);
    }
}