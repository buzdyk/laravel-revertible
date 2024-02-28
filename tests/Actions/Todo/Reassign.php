<?php

namespace Buzdyk\Revertible\Testing\Actions\Todo;

use Buzdyk\Revertible\BaseAction;
use Buzdyk\Revertible\Contracts\RevertibleUpdate;
use Buzdyk\Revertible\ExecuteUpdate;
use Buzdyk\Revertible\Models\Revertible;
use Buzdyk\Revertible\Testing\Models\Todo;

class Reassign extends BaseAction
{
    public function __construct(
        protected Todo $todo,
        protected int $assigneeId
    ) {}

    public function execute(): RevertibleUpdate
    {
        return tap(
            new ExecuteUpdate($this->todo->assignee_id, $this->assigneeId),
            fn (RevertibleUpdate $u) => $this->todo->update(['assignee_id' => $u->getResultValue()])
        );
    }

    public function revert(Revertible $revertible): void
    {
        $this->todo->update([
            'assignee_id' => $revertible->initial_value
        ]);
    }

    public function shouldExecute(): bool
    {
        return $this->todo->assignee_id !== $this->assigneeId;
    }

    public function shouldRevert(Revertible $revertible): bool
    {
        return $this->todo->assignee_id === $this->assigneeId;
    }
}