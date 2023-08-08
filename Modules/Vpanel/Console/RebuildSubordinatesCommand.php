<?php

namespace Modules\Vpanel\Console;

use Illuminate\Console\Command;
use Modules\Vpanel\Services\SubordinateService;

class RebuildSubordinatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vpanel:rebuild-subordinates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Пересобрать структуру подчинений для всех пользователей';


    /**
     * @throws \Throwable
     */
    public function handle(SubordinateService $subordinateService)
    {
        $subordinateService->totalRebuild();
    }
}