<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Models\Revertible;

abstract class BaseAction implements RevertibleAction
{
    abstract public function execute(): mixed;
    abstract public function revert(): mixed;

    public function onExecute(Revertible $revertible, mixed $executeReturn = null): Revertible
    {
        // persist action constructor parameters in $revertible->parameters
        $params = (new \ReflectionClass(static::class))
            ->getConstructor()
            ->getParameters();

        foreach ($params as $param) {
            $revertible->addParameter($param->name, $this->{$param->name});
        }

        return $revertible;
    }

    public function onRevert(Revertible $revertible, mixed $revertReturn = null): Revertible
    {
        return $revertible;
    }

    public function shouldExecute(): bool
    {
        return true;
    }

    public function shouldRevert(): bool
    {
        return true;
    }
}