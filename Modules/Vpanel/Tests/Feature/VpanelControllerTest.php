<?php

namespace Modules\Vpanel\Tests\Feature;

use Modules\Vpanel\Entities\Role;
use Modules\Vpanel\Entities\User;
use Tests\TestCase;

class VpanelControllerTest extends TestCase
{
    public function testGetList(): void
    {
        $this->setAuthUser();

        $this->get('/api/vpanel/Vpanel/User/list')->assertOk();
    }

    public function testGetRecord(): void
    {
        $this->setAuthUser();

        $record = User::factory()->create();
        $this->get('/api/vpanel/Vpanel/User/record/' . $record->id)->assertOk();
        $record->delete();
    }

    public function testSaveRecord(): void
    {
        $this->setAuthUser();

        $record = User::factory()->create();
        $this->post('/api/vpanel/Vpanel/User/save/' . $record->id, $record->toArray())->assertOk();
        $record->delete();
    }

    public function testDeleteRecord(): void
    {
        $this->setAuthUser();

        $record = User::factory()->create();
        $this->delete('/api/vpanel/Vpanel/User/delete/' . $record->id)->assertOk();
    }

    private function setAuthUser()
    {
        $user = User::query()
            ->whereJsonContains('role', Role::ROOT)
            ->get()
            ->first();

        $this->be($user);
    }

}
