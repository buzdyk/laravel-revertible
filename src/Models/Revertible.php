<?php

namespace Buzdyk\Revertible\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Revertible extends Model
{
    public $table = 'laravel_revertibles';

    protected $casts = [
        'parameters' => 'array'
    ];

    protected $guarded = [];

    public function revert(): self
    {
        $params = $this->getParameters();
        $class = $this->action_class;

        $action = new $class(...$params);

        return $action->revert($this);
    }

    public function getParametersAttribute($value): array
    {
        return $value ? json_decode($value, true) : [];
    }

    public function addParameter($key, mixed $value): Revertible
    {
        if ($value instanceof Model) {
            $value = "__eloquent__model:" . get_class($value) . ":$value->id";
        }

        $this->params[$key] = $value;

        return $this;
    }

    public function expandParameters(): Collection
    {
        $parameters = $this->parameters;

        foreach ($parameters as $name => $value) {
            $eloquentPrefix = "__eloquent__model:";

            if (is_string($value) && str_starts_with($eloquentPrefix, $value)) {
                list ($class, $id) = explode(":", str_replace($eloquentPrefix, "", $value));

                if (class_exists($class)) {
                    $params[$name] = $class::withTrashed()->find($id);
                }
            }
        }

        return collect($parameters);
    }
}