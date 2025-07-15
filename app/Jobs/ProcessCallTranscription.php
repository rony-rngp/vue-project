<?php

namespace App\Jobs;

use App\Models\CallRecord;
use App\Models\TicketConversation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ProcessCallTranscription implements ShouldQueue
{
    use Queueable;

    public $ticket_conversation_id;

    /**
     * Create a new job instance.
     */
    public function __construct($ticket_conversation_id)
    {
        $this->ticket_conversation_id = $ticket_conversation_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ticket_conversation = TicketConversation::find($this->ticket_conversation_id);

        if (!$ticket_conversation || !$ticket_conversation->recording_url) {
           info('Call record not found or missing URL: ID '.$this->ticket_conversation_id);
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . env('DEEPGRAM_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.deepgram.com/v1/listen?punctuate=true&diarize=true&smart_format=true', [
            'url' => $ticket_conversation->recording_url,
        ]);


        if ($response->successful()) {
            $result = $response->json();

            $words = $result['results']['channels'][0]['alternatives'][0]['words'] ?? [];

            $conversation = [];
            $current_speaker = null;
            $current_text = '';

            foreach ($words as $word) {
                if ($current_speaker === null) {
                    $current_speaker = $word['speaker'];
                }

                if ($word['speaker'] !== $current_speaker) {
                    $conversation[] = [
                        'speaker' => $current_speaker,
                        'text' => trim($current_text),
                    ];
                    $current_speaker = $word['speaker'];
                    $current_text = '';
                }

                $current_text .= ' ' . ($word['punctuated_word'] ?? $word['word']);
            }


            if ($current_text !== '') {
                $conversation[] = [
                    'speaker' => $current_speaker,
                    'text' => trim($current_text),
                ];
            }

            $ticket_conversation->transcription = json_encode($conversation);
            $ticket_conversation->save();

            info('Transcription completed for CallRecord ID: '.$ticket_conversation->id);
        } else {
            info('Deepgram API failed: '.$response->body());
        }
    }
}
