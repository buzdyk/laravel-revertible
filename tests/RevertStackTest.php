<?php

namespace Buzdyk\Revertible\Testing;

use Buzdyk\Revertible\ActionStack;
use Buzdyk\Revertible\RevertStack;
use Buzdyk\Revertible\Testing\Actions\NotRevertible;
use Buzdyk\Revertible\Testing\Actions\StatelessAction;
use Buzdyk\Revertible\Testing\Actions\ThrowExceptionOnRevert;
use Buzdyk\Revertible\Testing\Actions\Todo\ChangeStatus;
use Buzdyk\Revertible\Testing\Actions\Todo\Reassign;
use Buzdyk\Revertible\Models\Revertible;

class RevertStackTest extends TestCase
{
    /** @test */
    public function it_soft_deletes_actions_that_shouldnt_revert()
    {
        $uuid = ActionStack::make()
            ->push(new StatelessAction())
            ->push(new NotRevertible())
            ->push(new StatelessAction())
            ->execute();

        $this->assertEquals(3, Revertible::executed()->count());
        $this->assertEquals(0, Revertible::reverted()->count());

        RevertStack::make($uuid)->revert();

        $this->assertEquals(1, Revertible::executed()->where('reverted', false)->withTrashed()->whereNotNull('deleted_at')->count());
        $this->assertEquals(2, Revertible::reverted()->count());
    }

    /** @test */
    public function it_reverts_actions()
    {
        $todo = $this->createTodo();

        $this->assertEquals('new', $todo->status);
        $this->assertEquals(1, $todo->assignee_id);

        $uuid = ActionStack::make()
            ->push(new Reassign($todo, 5))
            ->push(new ChangeStatus($todo, 'accepted'))
            ->execute();

        $todo = $todo->fresh();

        $this->assertEquals(5, $todo->assignee_id);
        $this->assertEquals('accepted', $todo->status);

        RevertStack::make($uuid)->revert();

        $todo = $todo->fresh();

        $this->assertEquals('new', $todo->status);
        $this->assertEquals(1, $todo->assignee_id);
    }

    /** @test */
    public function it_reverts_transaction_on_exception_if_run_in_transaction_is_true()
    {
        $todo = $this->createTodo();

        $uuid = ActionStack::make()
            ->push(new Reassign($todo, 5))
            ->push(new ThrowExceptionOnRevert())
            ->push(new ChangeStatus($todo, 'accepted'))
            ->execute();

        $caughtException = false;

        try {
            RevertStack::make($uuid, true)->revert();
        } catch (\Exception $e) {
            $this->assertEquals('message', $e->getMessage());
            $caughtException = true;
        }

        $this->assertTrue($caughtException);
        $todo = $todo->fresh();

        $this->assertEquals(5, $todo->assignee_id);
        $this->assertEquals('accepted', $todo->status);
        $this->assertEquals(3, Revertible::executed()->count());
        $this->assertEquals(0, Revertible::reverted()->count());
    }

    /** @test */
    public function it_doesnt_revert_transaction_on_exception_if_run_in_transaction_is_false()
    {
        $todo = $this->createTodo();

        $uuid = ActionStack::make()
            ->push(new Reassign($todo, 5))
            ->push(new ThrowExceptionOnRevert())
            ->push(new ChangeStatus($todo, 'accepted'))
            ->execute();

        $caughtException = false;

        try {
            RevertStack::make($uuid, false)->revert();
        } catch (\Exception $e) {
            $this->assertEquals('message', $e->getMessage());
            $caughtException = true;
        }

        $this->assertTrue($caughtException);

        $todo = $todo->fresh();

        $this->assertEquals(5, $todo->assignee_id);
        $this->assertEquals('new', $todo->status);
        $this->assertEquals(3, Revertible::executed()->count());
        $this->assertEquals(1, Revertible::reverted()->count());
    }

}