<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Contracts\RevertibleUpdate;
use Buzdyk\Revertible\Models\Revertible;

abstract class BaseAction implements RevertibleAction
{
    abstract public function execute(): RevertibleUpdate;
    abstract public function revert(Revertible $revertible): void;

    public function onExecute(Revertible $revertible, RevertibleUpdate $update): void
    {
        // persist action constructor parameters in $revertible->parameters
        $params = (new \ReflectionClass(static::class))
            ->getConstructor()
            ->getParameters();

        foreach ($params as $param) {
            $revertible->addParameter($param->name, $this->{$param->name});
        }

        $revertible->initial_value = $update->getInitialValue();
        $revertible->result_value = $update->getResultValue();
    }

    public function onRevert(Revertible $revertible): void {}

    public function shouldExecute(): bool
    {
        return true;
    }

    public function shouldRevert(Revertible $revertible): bool
    {
        return true;
    }
}