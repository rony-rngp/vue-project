<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::with('contact')->latest()->paginate(10);
        return Inertia::render('admin/ticket/index',[
            'tickets' => $tickets
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contacts = Contact::get();
        return Inertia::render('admin/ticket/create',[
            'contacts' => $contacts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'subject' => 'required',
            'description' => 'required',
        ]);

        $ticket = new Ticket();
        $ticket->ticket_no = Ticket::createTicketNo();
        $ticket->subject = $request->subject;
        $ticket->contact_id = $request->contact_id;
        $ticket->description = $request->description;
        $ticket->save();

        return to_route('admin.tickets.index')->with('success', 'Ticket created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::with('contact')->find($id);
        return Inertia::render('admin/ticket/details',[
            'ticket' => $ticket,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $contacts = Contact::get();
        $ticket = Ticket::find($id);
        return Inertia::render('admin/ticket/edit',[
            'contacts' => $contacts,
            'ticket' => $ticket,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'subject' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);

        $ticket = Ticket::find($id);
        $ticket->subject = $request->subject;
        $ticket->contact_id = $request->contact_id;
        $ticket->description = $request->description;
        $ticket->status = $request->status;
        $ticket->save();

        return to_route('admin.tickets.index')->with('success', 'Ticket updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::find($id);
        $ticket->delete();
        return to_route('admin.tickets.index')->with('success', 'Ticket deleted successfully');
    }

    public function getCallerTicketList($id)
    {
        $tickets = Ticket::where('contact_id', $id)->latest()->take(10)->get();
        return response()->json($tickets);
    }

    public function getTicketDetails($id)
    {
        $ticket = Ticket::find($id);
        return response()->json($ticket);
    }

    public function ticketStore(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'contact_id' => 'required',
        ]);

        $ticket = new Ticket();
        $ticket->ticket_no = Ticket::createTicketNo();
        $ticket->subject = $request->subject;
        $ticket->contact_id = $request->contact_id;
        $ticket->description = $request->description;
        $ticket->save();

        return response()->json('Ticket added successfully');
    }

    public function updateTicketStatus(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = $request->status;
        $ticket->save();
        return response()->json($ticket);
    }
}
