<?php

namespace Buzdyk\Revertible;

use Buzdyk\Revertible\Contracts\RevertibleAction;
use Buzdyk\Revertible\Models\Revertible;
use Illuminate\Database\Eloquent\Collection;

class RevertStack
{
    protected Collection $revertibles;

    /**
     * @param $groupUuid
     * @return static
     */
    public static function make($groupUuid): static
    {
        $query = Revertible::query()
            ->where('group_uuid', $groupUuid)
            ->where('executed', true)
            ->where('reverted', false)
            ->orderBy('id', 'desc');

        return new static($query->get());
    }

    /**
     * @return Collection Collection of reverted Revertible models
     */
    public function revert(): Collection
    {
        /** @var Revertible $revertible */
        foreach ($this->revertibles as $revertible) {
            $action = $revertible->restoreAction();

            if ($action->shouldRevert($revertible) === false) {
                continue;
            }

            $action->revert($revertible);
            $action->onRevert($revertible);

            $revertible->reverted = true;
            $revertible->save();
        }

        return $this->revertibles->fresh();
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