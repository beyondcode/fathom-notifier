<?php

namespace App\Commands;

use BeyondCode\FathomAnalytics\FathomAnalytics;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;
use NunoMaduro\LaravelConsoleMenu\Menu;

class AddSiteCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the sites that will be notified.';

    public function handle(FathomAnalytics $analytics)
    {
        $sites = $analytics->getSites();

        /** @var Menu $menu */
        $menu = $this->menu('Which sites should be notified?', collect($sites)->mapWithKeys(function ($site) {
            return [$site->id => $site->name];
        })->toArray());

        $siteId = $menu->open();

        $site = collect($sites)->where('id', $siteId)->first();

        DB::table('sites')->updateOrInsert([
            'fathom_id' => $siteId,
        ], [
            'name' => $site->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Succesfully added {$site->name}.");
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
