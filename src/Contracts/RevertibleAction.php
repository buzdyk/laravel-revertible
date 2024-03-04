<?php
namespace Buzdyk\Revertible\Contracts;

use Buzdyk\Revertible\Models\Revertible;

interface RevertibleAction {
    public function setRevertible(Revertible $revertible): static;

    public function execute(): void;
    public function revert(): void;

    public function shouldExecute(): bool;
    public function shouldRevert(): bool;
}