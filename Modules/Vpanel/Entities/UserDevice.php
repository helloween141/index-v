<?php

namespace Modules\Vpanel\Entities;


class UserDevice extends base\UserDevice
{
    public static function getByToken(string $deviceId, string $authToken)
    {
        $query = static::query()
            ->where('device_id', '=', $deviceId)
            ->where('auth_token', '=', $authToken);

        return $query->first();
    }
}
