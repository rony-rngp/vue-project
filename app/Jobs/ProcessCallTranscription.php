<?php

namespace App\Jobs;

use App\Models\CallRecord;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ProcessCallTranscription implements ShouldQueue
{
    use Queueable;

    public $callRecordId;

    /**
     * Create a new job instance.
     */
    public function __construct($callRecordId)
    {
        $this->callRecordId = $callRecordId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $callRecord = CallRecord::find($this->callRecordId);

        if (!$callRecord || !$callRecord->recording_url) {
           info('Call record not found or missing URL: ID '.$this->callRecordId);
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . env('DEEPGRAM_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.deepgram.com/v1/listen?punctuate=true&smart_format=true', [
            'url' => $callRecord->recording_url,
        ]);

        if ($response->successful()) {
            $result = $response->json();
            $transcript = $result['results']['channels'][0]['alternatives'][0]['transcript'] ?? null;

            $callRecord->transcription = $transcript;
            $callRecord->save();

            info('✅ Transcription completed for CallRecord ID: '.$callRecord->id);
        } else {
            info('❌ Deepgram API failed: '.$response->body());
        }
    }
}
