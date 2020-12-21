<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class DeleteSiteCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:delete {site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete a configured site';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('sites')
            ->where('fathom_id', $this->argument('site'))
            ->delete();

        $this->info('Successfully deleted site.');
    }
}
