<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Number;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class ProcessCampaignChunks implements ShouldQueue
{
    use Queueable, Batchable;


    protected $campaignId;
    protected $numberListId;
    protected $chunkSize = 10000; // chunk size optimized for your server

    /**
     * Create a new job instance.
     */
    public function __construct($campaignId, $numberListId)
    {
        $this->campaignId = $campaignId;
        $this->numberListId = $numberListId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $campaign = Campaign::findOrFail($this->campaignId);

        $query = Number::where('number_list_id', $this->numberListId)
            ->select('phone_number');

        $total = $query->count();
        $campaign->update(['total_numbers' => $total]);

        $batchJobs = [];

        if ($total <= 10000 ){
            $this->chunkSize = 1000;
        }

        $query->chunk($this->chunkSize, function ($chunk) use (&$batchJobs, $campaign) {
            $numbers = $chunk->pluck('phone_number')->toArray();
            $batchJobs[] = new StoreCall($campaign->id, $numbers);
        });

        if (!empty($batchJobs)) {
            $batch = Bus::batch($batchJobs)
                ->then(function () use ($campaign) {
                    $campaign->update(['process_status' => 'Processed']);
                })
                ->catch(function () use ($campaign) {
                    $campaign->update(['process_status' => 'Failed']);
                })
                ->dispatch();

            $campaign->update(['batch_id' => $batch->id]);
        }
    }
}
