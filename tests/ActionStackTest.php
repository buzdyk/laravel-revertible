<?php

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Buzdyk\Revertible\Testing\Models\Todo;
use Buzdyk\Revertible\Models\Revertible;

class ActionStackTest extends TestCase
{
    use WithFaker;
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return ['Buzdyk\Revertible\ServiceProvider'];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__  . '/migrations');
    }

    /**
     * @test
     */
    public function testRecaptchaValidationPassesWithValidResponse()
    {
        $this->assertEquals(0, Revertible::count());
        $this->assertEquals(0, Todo::count());
    }
}
