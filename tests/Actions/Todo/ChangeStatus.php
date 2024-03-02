<?php

namespace Buzdyk\Revertible\Testing\Actions\Todo;

use Buzdyk\Revertible\BaseAction;
use Buzdyk\Revertible\Contracts\RevertibleUpdate;
use Buzdyk\Revertible\ActionUpdate;
use Buzdyk\Revertible\Models\Revertible;
use Buzdyk\Revertible\Testing\Models\Todo;

class ChangeStatus extends BaseAction
{
    public function __construct(
        protected Todo $todo,
        protected string $status
    ) {}

    public function execute(): RevertibleUpdate
    {
        return tap(
            new ActionUpdate($this->todo->status, $this->status),
            fn (RevertibleUpdate $u) => $this->todo->update(['status' => $u->getResultValue()])
        );
    }

    public function revert(Revertible $revertible): void
    {
        $this->todo->update([
            'status' => $revertible->initial_value
        ]);
    }

    public function shouldExecute(): bool
    {
        return $this->todo->status !== $this->status;
    }

    public function shouldRevert(Revertible $revertible): bool
    {
        return $this->todo->status === $this->status;
    }
}