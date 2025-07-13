<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCallTranscription;
use App\Models\CallRecord;
use App\Models\Contact;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConversationController extends Controller
{
    public function handel_conversation(Request $request)
    {
        $callId = $request->call_id;
        $caller = $request->caller;
        $recordingUrl = $request->recording_url;

        if (!$callId || !$caller || !$recordingUrl) {
            return response()->json(['message' => 'Missing required data'], 400);
        }

        $contact = Contact::where('caller_id', $caller)->first();

        $conversation = Conversation::firstOrCreate([
            'caller' => $caller,
            'contact_id' => $contact != null ? $contact->id : null,
        ]);

        $callRecord = new CallRecord();
        $callRecord->conversation_id = $conversation->id;
        $callRecord->call_id = $callId;
        $callRecord->recording_url = $recordingUrl;
        $callRecord->save();

        ProcessCallTranscription::dispatch($callRecord->id);

        return response()->json([
            'message' => 'Call record saved. Transcription queued.',
            'conversation_id' => $conversation->id,
            'call_record_id' => $callRecord->id
        ]);
    }


}
