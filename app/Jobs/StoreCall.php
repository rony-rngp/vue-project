<?php

namespace App\Jobs;

use App\Models\CampaignCall;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreCall implements ShouldQueue
{
    use Queueable, Batchable;

    public $campaign_id, $numbers;

    /**
     * Create a new job instance.
     */
    public function __construct($campaign_id, $numbers)
    {
        $this->campaign_id = $campaign_id;
        $this->numbers = $numbers;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->numbers as $number) {
            CampaignCall::create([
                'campaign_id' => $this->campaign_id,
                'number' => $number,
                'status' => 'pending'
            ]);
        }

    }
}
