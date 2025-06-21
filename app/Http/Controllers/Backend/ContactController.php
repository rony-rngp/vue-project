<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function contact_info_api($caller_id)
    {
        $contact = Contact::where('caller_id', $caller_id)->first();
        return response()->json(['contact' => $contact ?? null]);
    }

    public function contact_store_api(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'caller_id' => 'required|string|max:20|unique:contacts',
            'email' => 'nullable|email',
            'description' => 'nullable|string',
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->caller_id = $request->caller_id;
        $contact->email = $request->email;
        $contact->description = $request->description;
        $contact->user_id = Auth::id();
        $contact->save();

        return response()->json($contact);
    }
}
