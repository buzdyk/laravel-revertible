<?php

namespace Buzdyk\Revertible\Testing\Actions\Todo;

use Buzdyk\Revertible\BaseAction;
use Buzdyk\Revertible\Testing\Models\Todo;

class Reassign extends BaseAction
{
    public function __construct(
        protected Todo $todo,
        protected int $assigneeId
    ) {}

    public function execute(): void
    {
        $this->setValues($this->todo->assignee_id, $this->assigneeId);

        $this->todo->update(['assignee_id' => $this->assigneeId]);
    }

    public function revert(): void
    {
        $this->todo->update([
            'assignee_id' => $this->getInitialValue()
        ]);
    }

    public function shouldExecute(): bool
    {
        return $this->todo->assignee_id !== (int) $this->assigneeId;
    }

    public function shouldRevert(): bool
    {
        return $this->todo->assignee_id === $this->assigneeId;
    }
}