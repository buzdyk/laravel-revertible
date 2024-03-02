<?php

namespace Buzdyk\Revertible\Models;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Revertible extends Model
{
    public $table = 'laravel_revertibles';

    protected $casts = [
        'parameters' => 'array'
    ];

    protected $guarded = [];

    public function restoreAction(): RevertibleAction|null
    {
        if (! $this->executed) {
            return null;
        }

        $class = $this->action_class;
        $parameters = $this->expandParameters();

        $constructorParams = array_map(
            fn ($param) => $parameters[$param->name],
            (new \ReflectionClass($class))
                ->getConstructor()
                ->getParameters()
        );

        /** @var RevertibleAction $action */
        return new $class(...$constructorParams);
    }

    public function getParametersAttribute($value): array
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setConstructorParams(RevertibleAction $action)
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

        $this->constructor_params = $constructorParams;
    }

    public function getConstructorParams(): Collection
    {
        if (! ($params = $this->constructor_params)) {
            return collect();
        }

        foreach ($params as $name => $value) {
            $eloquentPrefix = "__eloquent__model:";

            if (is_string($value) && str_starts_with($eloquentPrefix, $value)) {
                list ($class, $id) = explode(":", str_replace($eloquentPrefix, "", $value));

                if (class_exists($class)) {
                    $params[$name] = $class::withTrashed()->find($id);
                }
            }
        }

        return collect($params);
    }
}