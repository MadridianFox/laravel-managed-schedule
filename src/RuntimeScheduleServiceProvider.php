<?php

namespace MadridianFox\LaravelRuntimeSchedule;

use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MadridianFox\LaravelRuntimeSchedule\Commands\ManagedScheduleRunCommand;
use MadridianFox\LaravelRuntimeSchedule\Listeners\OnTaskFinished;
use MadridianFox\LaravelRuntimeSchedule\Listeners\OnTaskStarted;

class RuntimeScheduleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Schedule::macro('replaceEvents', function (array $events) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->events = $events;
        });

        Event::listen(ScheduledTaskStarting::class, OnTaskStarted::class);
        Event::listen(ScheduledTaskFinished::class, OnTaskFinished::class);

        $this->commands([
            ManagedScheduleRunCommand::class,
        ]);
    }
}