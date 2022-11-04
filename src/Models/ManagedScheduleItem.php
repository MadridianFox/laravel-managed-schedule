<?php

namespace MadridianFox\LaravelRuntimeSchedule\Models;

use Illuminate\Console\Scheduling\Event as SchedulingEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property string $command
 * @property string $schedule
 * @property bool $enabled
 * @property Carbon $last_start_at
 * @property Carbon $last_end_at
 */
class ManagedScheduleItem extends Model
{
    use HasFactory;

    protected $table = 'managed_schedule_items';

    protected $dates = [
        'last_start_at',
        'last_end_at',
    ];

    public static function findBySchedulingEvent(SchedulingEvent $schedulingEvent): ?self
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        /** @var self $schedulingItem */
        $schedulingItem = self::query()
            ->where('command', $schedulingEvent->command)
            ->first();

        return $schedulingItem;
    }

    public static function createFromSchedulingEvent(SchedulingEvent $schedulingEvent): self
    {
        return tap(new self(), function (self $schedulingItem) use ($schedulingEvent) {
            $schedulingItem->schedule = $schedulingEvent->expression;
            $schedulingItem->command = $schedulingEvent->command;
            $schedulingItem->enabled = true;

            $schedulingItem->save();
        });
    }
}
