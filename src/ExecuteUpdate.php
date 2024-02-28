<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleUpdate;

class ExecuteUpdate implements RevertibleUpdate {

    public function __construct(
        public mixed $initialValue,
        public mixed $resultValue
    ) {}

    public function getInitialValue(): mixed
    {
        return $this->initialValue;
    }

    public function getResultValue(): mixed
    {
        return $this->resultValue;
    }
}