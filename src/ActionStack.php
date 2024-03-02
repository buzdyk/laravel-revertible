<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Models\Revertible;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use DB;
use Exception;

class ActionStack
{
    protected string $uuid;
    protected Collection $actions;

    protected function __construct(
        protected bool $runInTransaction = false
    ) {
        // todo? allow a custom uuid generator
        // todo guarantee uniqueness for unlikely collisions
        $this->uuid = Str::random(60);
        $this->actions = new Collection();
    }

    public static function make($runInTransaction = false): static
    {
        return new static($runInTransaction);
    }

    public function push(RevertibleAction $action): self
    {
        return tap($this, fn () => $this->actions->push($action));
    }

    /**
     * @throws \Exception
     */
    public function execute(): string
    {
        try {
            $this->runInTransaction && DB::beginTransaction();

            /** @var RevertibleAction $action */
            while ($action = $this->actions->shift()) {
                if ($action->shouldExecute() !== true) {
                    continue;
                }

                $revertible = new Revertible([
                    'group_uuid' => $this->uuid,
                ]);
                $revertible->setActionContext($action);

                $action->onExecute(
                    $revertible,
                    $action->execute()
                );

                $revertible->executed = true;
                $revertible->save();
            }

            $this->runInTransaction && DB::commit();
        } catch (\Exception $e) {
            $this->runInTransaction && DB::rollback();
            throw $e;
        }

        return $this->uuid;
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