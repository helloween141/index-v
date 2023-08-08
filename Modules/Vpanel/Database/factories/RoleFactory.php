<?php

namespace Modules\Vpanel\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Vpanel\Entities\Role;

class RoleFactory extends Factory
{
    protected $model = Role::class;
    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        return [];
    }
}
