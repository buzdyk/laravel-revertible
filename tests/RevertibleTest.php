<?php

namespace Buzdyk\Revertible\Testing;

use Buzdyk\Revertible\Models\Revertible;
use Buzdyk\Revertible\Testing\Actions\StatelessAction;
use Buzdyk\Revertible\Testing\Actions\Todo\Reassign;
use Buzdyk\Revertible\Testing\Models\Todo;
use Buzdyk\Revertible\Testing\Models\TrashableTodo;

class RevertibleTest extends TestCase
{
    /** @test */
    public function it_persists_eloquent_model_in_constructor_params()
    {
        $todo = $this->createTodo();
        $action = new Reassign($todo, 5);
        $revertible = new Revertible();
        $revertible->setActionContext($action);

        $params = $revertible->constructor_params;

        $this->assertIsArray($params);
        $this->assertArrayHasKey('assigneeId', $params);
        $this->assertEquals(5, $params['assigneeId']);
        $this->assertArrayHasKey('todo', $params);
        $this->assertEquals('__eloquent__model:' . $todo::class . ':' .  $todo->id, $params['todo']);
    }

    /** @test */
    public function it_persists_zero_constructor_params()
    {
        $action = new StatelessAction();
        $revertible = new Revertible();
        $revertible->setActionContext($action);

        $params = $revertible->constructor_params;

        $this->assertIsArray($params);
        $this->assertEmpty($params);
    }

    /** @test */
    public function it_restores_action_with_eloquent_model_in_constructor_params()
    {
        $todo = $this->createTodo();
        $action = new Reassign($todo, 5);
        $revertible = new Revertible();
        $revertible->setActionContext($action);

        $restoredAction = $revertible->restoreAction();

        $todoProp = new \ReflectionProperty($action::class, 'todo');
        $todoPropValue = $todoProp->getValue($restoredAction);
        $this->assertInstanceOf(Todo::class, $todoPropValue);
        $this->assertEquals($todo->id, $todoPropValue->id);

        $assigneeProp = new \ReflectionProperty($action::class, 'assigneeId');
        $this->assertEquals(5, $assigneeProp->getValue($restoredAction));
    }

    /** @test */
    public function it_restores_action_with_zero_constructor_params()
    {
        $action = new StatelessAction();
        $revertible = new Revertible();
        $revertible->setActionContext($action);

        $restoredAction = $revertible->restoreAction();

        $this->assertInstanceOf($action::class, $restoredAction);
    }

    /** @test */
    public function it_restores_action_with_a_soft_deleted_models()
    {
        $todo = TrashableTodo::create([
            'assignee_id' => 1,
            'status' => 'new',
            'title' => 'yes',
        ]);

        $action = new Reassign($todo, 5);
        $revertible = new Revertible();
        $revertible->setActionContext($action);

        $todo->delete();

        $restoredAction = $revertible->restoreAction();

        $prop = new \ReflectionProperty($action::class, 'todo');
        $value = $prop->getValue($restoredAction);

        $this->assertEquals($todo->id, $value->id);
        $this->assertNotNull($todo->deleted_at);
    }
}