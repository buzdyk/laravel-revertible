<?php

namespace Buzdyk\Revertible\Testing\Actions;

use Buzdyk\Revertible\BaseAction;

class StatelessAction extends BaseAction
{
    public function __construct() {}

    public function execute(): void
    {

    }

    public function revert(): void
    {
        //..
    }
}