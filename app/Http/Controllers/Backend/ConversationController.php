<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCallTranscription;
use App\Models\CallRecord;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Ticket;
use App\Models\TicketConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

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

        $current_ticket = Ticket::where('id', $contact->current_ticket)->first();

        if ($current_ticket){
            $ticket_conversation = new TicketConversation();
            $ticket_conversation->ticket_id = $current_ticket->id;
            $ticket_conversation->call_id = $callId;
            $ticket_conversation->recording_url = $recordingUrl;
            $ticket_conversation->save();

            ProcessCallTranscription::dispatch($ticket_conversation->id);

            return response()->json([
                'message' => 'Ticket Conversation saved. Transcription queued.',
                'ticket_id' => $current_ticket->id,
                'TicketConversation' => $ticket_conversation->id
            ]);
        }else{
            return response()->json([
                'message' => 'No ticket selected',
            ]);
        }

    }

    public function index()
    {
        $conversations = Conversation::with('contact')->withCount('call_records')->latest()->paginate('10');
        return Inertia::render('admin/conversations/index', [
            'conversations' => $conversations
        ]);
    }

    /*public function show($id)
    {
        $conversation = Conversation::with('contact')->find($id);
        $call_records = CallRecord::where('conversation_id', $conversation->id)->latest()->paginate(20);
        return Inertia::render('admin/conversations/details',[
            'conversation' => $conversation,
            'call_records' => $call_records,
        ]);
    }*/


}
