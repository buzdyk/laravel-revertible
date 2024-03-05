<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Models\Revertible;

abstract class BaseAction implements RevertibleAction
{
    // only BaseAction should know details about Revertible implementation
    private Revertible $revertible;

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

    protected function setValues(mixed $initialValue = null, mixed $resultValue = null): static
    {
        $this->revertible->initial_value = $initialValue;
        $this->revertible->result_value = $resultValue;

        return $this;
    }

    protected function getInitialValue(): mixed
    {
        return $this->revertible->initial_value;
    }

    protected function getResultValue(): mixed
    {
        return $this->revertible->result_value;
    }

    protected function setActorId(mixed $actorId): static
    {
        $this->revertible->actor_id = $actorId;

        return $this;
    }

    protected function getActorId(): int
    {
        return $this->revertible->actor_id;
    }
}