<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;

class AnalyticsResult extends Notification
{
    protected $data;
    protected $comparisonData;
    protected $site;

    /** @var Carbon */
    protected $date;

    /** @var Carbon */
    protected $comparisonDate;

    public function __construct($site, $date, $comparisonDate, $data, $comparisonData)
    {
        $this->site = $site;
        $this->date = $date;
        $this->comparisonDate = $comparisonDate;
        $this->data = $data;
        $this->comparisonData = $comparisonData;
    }

    public function via()
    {
        return ['slack'];
    }

    protected function getGrowth()
    {
        $previousUsers = $this->comparisonData->site_stats->current->visits;

        return (100 - ($this->data->site_stats->current->visits / $previousUsers * 100)) * -1;
    }

    public function toSlack($notifiable)
    {
        $previousUsers = $this->comparisonData->site_stats->current->visits;

        $growth = $this->getGrowth();
        $notification = "Yesterday, *{$this->site->name}* had *{$this->data->site_stats->current->visits}* users.\n";
        $growthPerception = $growth > 0 ? 'better' : 'worse';
        $growthNumber = round(abs($growth));

        $notification .= "That's *{$growthNumber}%* {$growthPerception} than last {$this->date->dayName}.";

        return (new SlackMessage())
            ->content($notification)
            ->attachment(function (SlackAttachment $attachment) use ($growth) {
                $attachment
                    ->color($growth > 0 ? '#36a64f' : '#FF0000')
                    ->fields([
                        $this->date->format('d. M') . ' ('. ($growth > 0 ? '↑' : '↓') .')' => $this->data->site_stats->current->visits,
                        $this->comparisonDate->format('d. M') => $this->comparisonData->site_stats->current->visits,
                    ]);
            })
            ->attachment(function (SlackAttachment $attachment) {
                $topReferrers = array_slice($this->data->referrer_stats, 0, 5);
                $attachment
                    ->title('Top Referrers')
                    ->fields(collect($topReferrers)->mapWithKeys(function ($ref) {
                        return [$ref->groupName ?? $ref->hostname_name => $ref->uniques];
                    })->toArray());
            });
    }
}
