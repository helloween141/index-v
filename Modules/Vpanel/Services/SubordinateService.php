<?php

namespace Modules\Vpanel\Services;

use Modules\Vpanel\Entities\User;

class SubordinateService
{
    /**
     * @throws \Throwable
     */
    public function totalRebuild(): void
    {
        $users = User::query()->get();
        foreach ($users as $user) {
            /** @var User $user */
            $user->subordinates_ids = implode(',', $user->getSubordinates(fromCache: false, onlyIds: true));
            $user->save();
        }
    }

}
