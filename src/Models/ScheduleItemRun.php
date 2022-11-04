<?php

namespace MadridianFox\LaravelRuntimeSchedule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Console\Scheduling\Event as SchedulingEvent;

/**
 * @property int $id
 * @property string $command
 * @property string $schedule
 * @property Carbon $started_at
 * @property Carbon|null $finished_at
 * @property int|null $exit_code
 */
class ScheduleItemRun extends Model
{
    private static $runRecordIds = [];

    protected $table = 'schedule_item_runs';

    public $timestamps = false;

    protected $dates = [
        'started_at',
        'finished_at',
    ];

    public static function createFromStartedTask(SchedulingEvent $task): void
    {
        $runRecord = new self();
        $runRecord->command = $task->command;
        $runRecord->schedule = $task->expression;
        $runRecord->started_at = now();

        $runRecord->save();

        self::$runRecordIds[$runRecord->command] = $runRecord->id;
    }

    public static function updateByFinishedTask(SchedulingEvent $task): void
    {
        $runRecordId = self::$runRecordIds[$task->command] ?? null;
        if (!$runRecordId) {
            return;
        }

        /** @var self $runRecord */
        $runRecord = self::query()->find($runRecordId);
        if (!$runRecord) {
            return;
        }

        $runRecord->finished_at = now();
        $runRecord->exit_code = $task->exitCode;
        $runRecord->save();
    }

    public static function deleteOldRecords(string $command, int $keepRecordsCount = 5): void
    {
        self::query()
            ->where('command', $command)
            ->whereNotIn('id', function (Builder $query) use ($command, $keepRecordsCount) {
                $tableName = (new ScheduleItemRun())->getTable();
                return $query->from($tableName)
                    ->where('command', $command)
                    ->select(['id'])
                    ->orderBy('id', 'desc')
                    ->limit($keepRecordsCount);
            })
            ->delete();
    }
}