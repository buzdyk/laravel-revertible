<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Models\Revertible;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ActionStack
{
    protected string $uuid;

    public function __construct(
        protected Collection $actions = new Collection(),
    ) {
        // todo custom uuid generator
        $this->uuid = Str::random();
    }

    public static function make(): static
    {
        return new static;
    }

    public function push(RevertibleAction $action): self
    {
        return tap($this, fn () => $this->actions->push($action));
    }

    public function execute(): RevertStack
    {
        /** @var RevertibleAction $action */
        while ($action = $this->actions->shift()) {
            if ($action->shouldExecute() !== true) {
                continue;
            }

            $revertible = new Revertible([
                'group_uuid' => $this->uuid,
                'action_class' => static::class,
            ]);

            $action->onExecute($revertible, $action->execute());

            $revertible->executed = true;
            $revertible->save();
        }

        return RevertStack::make($this->uuid);
    }

    public function collection(): Collection
    {
        return $this->actions;
    }

    public function count(): int
    {
        return $this->actions->count();
    }
}