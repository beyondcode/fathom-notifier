<?php

namespace App\Commands;

use App\Notifications\AnalyticsResult;
use BeyondCode\FathomAnalytics\FathomAnalytics;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use LaravelZero\Framework\Commands\Command;

class NotifyStatistics extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'notify';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Send Fathom notifications';

    public function handle(FathomAnalytics $analytics)
    {
        $sites = DB::table('sites')->get();

        foreach ($sites as $site) {
            $this->info('Notifying '.$site->name);

            $date = today()->subDay();
            $comparisonDate = today()->subDay()->subWeek();
            $data = $analytics->getData($site->fathom_id, $date->startOfDay(), $date->endOfDay());
            $comparisonData = $analytics->getData($site->fathom_id, $comparisonDate->startOfDay(), $comparisonDate->endOfDay());

            Notification::route('slack', env('SLACK_URL'))
                ->notify(new AnalyticsResult($site, $date, $comparisonDate, $data, $comparisonData));
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->dailyAt(config('fathom.notify_at'));
    }
}
