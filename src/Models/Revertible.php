<?php

namespace Buzdyk\Revertible\Models;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class Revertible extends Model
{
    use SoftDeletes;

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
        $constructorParameters = $this->getConstructorParams();

        return new $class(...array_map(
            fn ($param) => $constructorParameters[$param->name],
            (new \ReflectionClass($class))
                ->getConstructor()
                ->getParameters()
        ));
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

    public function scopeReverted(Builder $query): void
    {
        $query->where('reverted', true);
    }

    public function scopeExecuted(Builder $query): void
    {
        $query->where('executed', true);
    }
}