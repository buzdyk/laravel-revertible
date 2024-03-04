<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Models\Revertible;

abstract class BaseAction implements RevertibleAction
{
    private Revertible $revertible; // only BaseAction should know details about Revertible implementation

    abstract public function execute(): void;
    abstract public function revert(): void;

    public function shouldExecute(): bool
    {
        return true;
    }

    public function shouldRevert(): bool
    {
        return true;
    }

    public function setRevertible(Revertible $revertible): static
    {
        return tap($this, fn () => $this->revertible = $revertible);
    }

    protected function setValues(mixed $initialValue, mixed $resultValue): static
    {
        $this->revertible->initial_value = $initialValue;
        $this->revertible->result_value = $resultValue;

        return $this;
    }

    protected function setActorId(int $actorId)
    {
        $this->revertible->actor_id = $actorId;
    }

    protected function getInitialValue()
    {
        return $this->revertible->initial_value;
    }

    protected function getResultValue()
    {
        return $this->revertible->result_value;
    }

    protected function getActorId()
    {
        return $this->revertible->actor_id;
    }
}