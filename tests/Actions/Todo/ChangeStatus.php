<?php

namespace Buzdyk\Revertible\Testing\Actions\Todo;

use Buzdyk\Revertible\BaseAction;
use Buzdyk\Revertible\Testing\Models\Todo;

class ChangeStatus extends BaseAction
{
    public function __construct(
        protected Todo $todo,
        protected string $status
    ) {}

    public function execute(): void
    {
        $this->setValues($this->todo->status, $this->status);

        $this->todo->update(['status' => $this->status]);
    }

    public function revert(): void
    {
        $this->todo->update([
            'status' => $this->getInitialValue()
        ]);
    }

    public function shouldExecute(): bool
    {
        return $this->todo->status !== $this->status;
    }

    public function shouldRevert(): bool
    {
        return $this->todo->status === $this->status;
    }
}