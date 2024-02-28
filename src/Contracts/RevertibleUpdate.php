<?php

namespace Buzdyk\Revertible\Contracts;

interface RevertibleUpdate {
    public function getInitialValue(): mixed;
    public function getResultValue(): mixed;
}