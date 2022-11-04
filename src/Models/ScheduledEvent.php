<?php

namespace MadridianFox\LaravelRuntimeSchedule\Models;

use Illuminate\Console\Scheduling\Event;
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
class ScheduledEvent extends Model
{
    use HasFactory;

    protected $dates = [
        'last_start_at',
        'last_end_at',
    ];

    public static function findByEvent(Event $event): ?self
    {
        /** @var self $event */
        $event = self::query()
            ->where('command', $event->command)
            ->first();

        return $event;
    }
}
