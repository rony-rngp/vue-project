<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\OdooService;
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

        try {
            $odoo = new OdooService();

            $result = $odoo->syncContact($request->caller_id, [
                'name' => $request->name,
                'email' => $request->email,
                'company_type' => 'person'
            ]);

            if($result['id']){
                $contact = new Contact();
                $contact->odoo_contact_id = $result['id'];
                $contact->name = $request->name;
                $contact->caller_id = $request->caller_id;
                $contact->email = $request->email;
                $contact->description = $request->description;
                $contact->user_id = Auth::id();
                $contact->save();
                return response()->json(['status' => true, 'contact' => $contact]);
            }else{
                return response()->json(['status' => false, 'message' => 'Failed to create contact']);
            }

        }catch (\Exception $e){
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }


    }
}
