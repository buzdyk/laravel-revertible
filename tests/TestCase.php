<?php

namespace Buzdyk\Revertible\Testing;

use Buzdyk\Revertible\Testing\Models\Todo;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function getPackageProviders($app)
    {
        return ['Buzdyk\Revertible\ServiceProvider'];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__  . '/migrations');
    }

    protected function createTodo()
    {
        $todo = new Todo([
            'assignee_id' => 1,
            'status' => 'new',
            'title' => $this->faker->words(4, true),
        ]);

        return tap($todo)->save();
    }
}