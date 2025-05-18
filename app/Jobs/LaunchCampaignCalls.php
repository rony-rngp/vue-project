<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignCall;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LaunchCampaignCalls implements ShouldQueue
{
    use Queueable;

    public $campaign;
    protected $batchSize = 100;

    /**
     * Create a new job instance.
     */
    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->campaign->status !== 'running') return;

        $this->campaign->calls()
            ->where('status', 'pending')
            ->orderBy('id')
            ->limit($this->batchSize)
            ->each(function ($call) {
                $this->processCall($call);
            });

        // Dispatch next batch if there are still pending calls
        if ($this->campaign->calls()->where('status', 'pending')->exists()) {
            LaunchCampaignCalls::dispatch($this->campaign)->delay(now()->addSeconds(5));
        } else {
            // ✅ All calls completed, update campaign status
            $this->campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

    }

    protected function processCall(CampaignCall $call)
    {
        $number = $call->number;
        $voiceFile = basename($this->campaign->voice_file->file); // stored filename only

        // Set variables for Asterisk
      /*  $variables = [
            "CAMPAIGN_ID={$this->campaign->id}",
            "CALL_ID={$call->id}",
        ];

        // Handle DTMF options
        if (is_array($this->campaign->dtmf_options)) {
            foreach ($this->campaign->dtmf_options as $key => $action) {
                $variables[] = "DTMF_{$key}={$action}";
            }
        }

        $setVariables = implode("\nSet: ", $variables);

        // Create .call file
        $callFile = <<<EOD
            Channel: SIP/{$number}@yourprovider
            CallerID: "Campaign" <1000>
            MaxRetries: 1
            RetryTime: 60
            WaitTime: 30
            Context: from-internal
            Extension: s
            Priority: 1
            Set: {$setVariables}
            Application: Playback
            Data: /var/lib/asterisk/sounds/{$voiceFile}
            EOD;

        $filename = 'call_' . time() . '_' . $call->id . ".call";
        $tempPath = storage_path("app/callfiles/$filename"); // optional subdir
        file_put_contents($tempPath, $callFile);

        rename($tempPath, "/var/spool/asterisk/outgoing/$filename");*/

        // Update status
        $call->update([
            'status' => 'dialed',
            'called_at' => now(),
        ]);

        // Increment processed count
        Campaign::where('id', $this->campaign->id)->increment('processed_numbers');

    }
}

