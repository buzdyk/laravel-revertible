<?php

namespace Buzdyk\Revertible\Models;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Revertible extends Model
{
    public $table = 'laravel_revertibles';

    protected $casts = [
        'constructor_params' => 'array'
    ];

    protected $fillable = [
        'group_uuid',
    ];

    public function restoreAction(): RevertibleAction|null
    {
        $class = $this->action_class;
        $parameters = $this->getConstructorParams();

        $constructorParams = array_map(
            fn ($param) => $parameters[$param->name],
            (new \ReflectionClass($class))
                ->getConstructor()
                ->getParameters()
        );

        /** @var RevertibleAction $action */
        return new $class(...$constructorParams);
    }

    public function setActionContext(RevertibleAction $action): static
    {
        $constructorParams = [];

        // persist action constructor parameters in $revertible->parameters
        $params = (new \ReflectionClass($action::class))
            ->getConstructor()
            ->getParameters();

        foreach ($params as $param) {
            $prop = new \ReflectionProperty($action::class, $param->name);
            $value = $prop->getValue($action);

            $constructorParams[$param->name] = $value instanceof Model
                ? "__eloquent__model:" . get_class($value) . ":" . $value->getKey()
                : $value;
        }

        $this->action_class = $action::class;
        $this->constructor_params = $constructorParams;

        return $this;
    }

    protected function getConstructorParams(): Collection
    {
        if (! ($params = $this->constructor_params)) {
            return collect();
        }

        foreach ($params as $name => $value) {
            $eloquentPrefix = "__eloquent__model:";

            if (is_string($value) && str_starts_with($value, $eloquentPrefix)) {
                list ($class, $id) = explode(":", str_replace($eloquentPrefix, "", $value));

                if (class_exists($class)) {
                    $query = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(new $class))
                        ? $class::withTrashed()
                        : $class::query();

                    $params[$name] = $query->find($id);
                }
            }
        }

        return collect($params);
    }
}