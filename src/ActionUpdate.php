<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleUpdate;

class ActionUpdate implements RevertibleUpdate {

    public function __construct(
        public mixed $initialValue = null,
        public mixed $resultValue = null,
        public array $context = [],
    ) {}

    public function getInitialValue(): mixed
    {
        return $this->initialValue;
    }

    public function getResultValue(): mixed
    {
        return $this->resultValue;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}