<?php

namespace MadridianFox\LaravelRuntimeSchedule\Listeners;

use Illuminate\Console\Events\ScheduledTaskStarting;
use MadridianFox\LaravelRuntimeSchedule\Models\ManagedScheduleItem;
use MadridianFox\LaravelRuntimeSchedule\Models\ScheduleItemRun;

class OnTaskStarted
{
    public function handle(ScheduledTaskStarting $event)
    {
        $storedEvent = ManagedScheduleItem::findBySchedulingEvent($event->task);
        $storedEvent->saveLastStarted();

        ScheduleItemRun::createFromStartedTask($event->task);
    }
}