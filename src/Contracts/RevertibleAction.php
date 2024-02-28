<?php
namespace Buzdyk\Revertible\Contracts;

use Buzdyk\Revertible\Models\Revertible;

interface RevertibleAction {
    public function execute(): RevertibleUpdate;
    public function revert(Revertible $revertible): void;

    public function onExecute(Revertible $revertible, RevertibleUpdate $update): void;
    public function onRevert(Revertible $revertible): void;

    public function shouldExecute(): bool;
    public function shouldRevert(Revertible $revertible): bool;
}