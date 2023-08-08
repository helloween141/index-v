<?php

namespace Modules\Vpanel\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            /** @var Schedule $schedule */
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('vpanel:rebuild-permissions')->daily();
            $schedule->command('vpanel:rebuild-subordinates')->daily();
        });
    }
}
