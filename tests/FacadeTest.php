<?php

namespace Buzdyk\Revertible\Testing;

class FacadeTest extends TestCase
{
    /** @test */
    public function revertible_facade_exists()
    {
        \Revertible::actionStack();
    }
}