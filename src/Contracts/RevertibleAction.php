<?php
namespace Buzdyk\Revertible\Contracts;

use Buzdyk\Revertible\Models\Revertible;

interface RevertibleAction {
    public function execute(): mixed;
    public function revert(): mixed;

    public function onExecute(Revertible $revertible, mixed $executeReturn = null): Revertible;
    public function onRevert(Revertible $revertible, mixed $revertReturn = null): Revertible;

    public function shouldExecute(): bool;
    public function shouldRevert(): bool;
}