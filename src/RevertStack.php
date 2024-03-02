<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Models\Revertible;
use Illuminate\Database\Eloquent\Collection;
use DB;

class RevertStack
{
    protected function __construct(
        protected Collection $revertibles,
        protected bool $runInTransaction = false
    ) {}

    public function runInTransaction($runInTransaction = true): static
    {
        return tap($this, fn() => $this->runInTransaction = $runInTransaction);
    }

    /**
     * @param $groupUuid
     * @return static
     */
    public static function make($groupUuid, $runInTransaction = false): static
    {
        $query = Revertible::query()
            ->where('group_uuid', $groupUuid)
            ->where('executed', true)
            ->where('reverted', false)
            ->orderBy('id', 'desc');

        return new static($query->get(), $runInTransaction);
    }

    /**
     * @return Collection Collection of reverted Revertible models
     * @throws \Exception
     */
    public function revert(): Collection
    {
        try {
            $this->runInTransaction && DB::beginTransaction();

            /** @var Revertible $revertible */
            foreach ($this->revertibles as $revertible) {
                $action = $revertible->restoreAction();

                if ($action->shouldRevert($revertible) === false) {
                    $revertible->delete();
                    continue;
                }

                $action->revert($revertible);
                $action->onRevert($revertible);

                $revertible->reverted = true;
                $revertible->save();
            }

            DB::commit();

            return $this->revertibles->fresh();
        } catch (\Exception $e) {
            $this->runInTransaction && DB::rollback();
            throw $e;
        }
    }

    public function collection()
    {
        return $this->revertibles;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->revertibles->count();
    }
}