<?php

namespace MadridianFox\LaravelRuntimeSchedule\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use MadridianFox\LaravelRuntimeSchedule\Models\ManagedScheduleItem;

class ManagedScheduleRunCommand extends ScheduleRunCommand
{
    protected $signature = 'schedule:run';

    public function handle(Schedule $schedule, Dispatcher $dispatcher, ExceptionHandler $handler)
    {
        $schedule->replaceEvents($this->mergeEventsFromDb($schedule));

        parent::handle($schedule, $dispatcher, $handler);
    }

    private function mergeEventsFromDb(Schedule $schedule): array
    {
        $storedEvents = ManagedScheduleItem::all()->keyBy('command');
        $actualEvents = [];

        foreach ($schedule->events() as $event) {
            /** @var ManagedScheduleItem|null $storedEvent */
            $storedEvent = $storedEvents->get($event->command);
            if (!$storedEvent) {
                $storedEvent = ManagedScheduleItem::createFromSchedulingEvent($event);
            }

            if (!$storedEvent->enabled) {
                continue;
            }

            $event->expression = $storedEvent->schedule;
            $actualEvents[] = $event;
        }

        return $actualEvents;
    }
}