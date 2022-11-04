<?php

namespace MadridianFox\LaravelRuntimeSchedule\Commands;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use MadridianFox\LaravelRuntimeSchedule\Models\ScheduledEvent;

class RuntimeScheduleRunCommand extends ScheduleRunCommand
{
    protected $signature = 'runtime-schedule:run';

    public function handle(Schedule $schedule, Dispatcher $dispatcher, ExceptionHandler $handler)
    {
        $schedule->replaceEvents($this->mergeEventsFromDb($schedule));

        parent::handle($schedule, $dispatcher, $handler);
    }

    private function mergeEventsFromDb(Schedule $schedule): array
    {
        $storedEvents = ScheduledEvent::all()->keyBy('command');
        $actualEvents = [];

        foreach ($schedule->events() as $event) {
            /** @var ScheduledEvent|null $storedEvent */
            $storedEvent = $storedEvents->get($event->command);
            if (!$storedEvent) {
                $storedEvent = $this->saveNewEvent($event);
            }

            if (!$storedEvent->enabled) {
                continue;
            }

            $event->expression = $storedEvent->schedule;
            $actualEvents[] = $event;
        }

        return $actualEvents;
    }

    private function saveNewEvent(Event $event): ScheduledEvent
    {
        $storedEvent = new ScheduledEvent();
        $storedEvent->schedule = $event->expression;
        $storedEvent->command = $event->command;
        $storedEvent->enabled = true;

        $storedEvent->save();

        return $storedEvent;
    }
}