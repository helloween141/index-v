<?php

namespace Modules\Vpanel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Vpanel\Entities\Role;
use Modules\Vpanel\Entities\User;
use Psy\Util\Json;

class VpanelDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::factory()->create([
            'name' => Role::ROOT,
            'title' => 'Администратор',
            'description' => 'Администратор'
        ]);

        User::factory()->create([
            'name' => 'Администратор',
            'login' => 'admin',
            'email' => 'admin@test.ru',
            'password' => bcrypt('admin'),
            'role' => Json::encode([Role::ROOT]),
            'has_access' => true
        ]);
    }
}
