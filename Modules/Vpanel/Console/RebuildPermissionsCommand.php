<?php

namespace Modules\Vpanel\Console;

use Illuminate\Console\Command;
use Modules\Vpanel\Services\RbacService;

class RebuildPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vpanel:rebuild-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Пересобрать права доступа для всех пользователей';


    /**
     * @throws \Throwable
     */
    public function handle(RbacService $rbacService)
    {
        $rbacService->totalRebuild();
    }
}