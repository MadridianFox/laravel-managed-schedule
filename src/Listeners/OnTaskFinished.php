<?php

namespace MadridianFox\LaravelRuntimeSchedule\Listeners;

use Illuminate\Console\Events\ScheduledTaskFinished;
use MadridianFox\LaravelRuntimeSchedule\Models\ManagedScheduleItem;
use MadridianFox\LaravelRuntimeSchedule\Models\ScheduleItemRun;

class OnTaskFinished
{
    public function handle(ScheduledTaskFinished $event)
    {
        $storedEvent = ManagedScheduleItem::findBySchedulingEvent($event->task);
        $storedEvent->saveLastFinished();

        ScheduleItemRun::updateByFinishedTask($event->task);
        ScheduleItemRun::deleteOldRecords($event->task->command);
    }
}