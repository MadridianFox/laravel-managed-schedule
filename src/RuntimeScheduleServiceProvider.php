<?php

namespace MadridianFox\LaravelRuntimeSchedule;

use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MadridianFox\LaravelRuntimeSchedule\Commands\RuntimeScheduleRunCommand;
use MadridianFox\LaravelRuntimeSchedule\Models\ScheduledEvent;

class RuntimeScheduleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Schedule::macro('replaceEvents', function (array $events) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->events = $events;
        });

        Event::listen(ScheduledTaskStarting::class, function (ScheduledTaskStarting $event) {
            $storedEvent = ScheduledEvent::findByEvent($event->task);
            if (!$storedEvent) {
                return;
            }
            $storedEvent->last_start_at = now();
            $storedEvent->save();
        });

        Event::listen(ScheduledTaskFinished::class, function (ScheduledTaskFinished $event) {
            $storedEvent = ScheduledEvent::findByEvent($event->task);
            if (!$storedEvent) {
                return;
            }
            $storedEvent->last_end_at = now();
            $storedEvent->save();
        });

        $this->commands([
            RuntimeScheduleRunCommand::class,
        ]);
    }
}