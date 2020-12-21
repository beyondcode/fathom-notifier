<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class ListSitesCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all configured sites';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->table(['Fathom ID', 'Name'], DB::table('sites')->get(['fathom_id', 'name'])->map(function ($site) {
            return (array)$site;
        }));
    }
}
