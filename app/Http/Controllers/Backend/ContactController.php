<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\OdooService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactController extends Controller
{

    public function index()
    {
        $contacts = Contact::latest()->paginate(10);
        return Inertia::render('admin/contact/index',[
            'contacts' => $contacts
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/contact/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'caller_id' => 'required|numeric|unique:contacts',
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

                return to_route('admin.contacts.index')->with('success', 'Contact added successfully');

            }else{
                return redirect()->back()->with('error', 'Failed to create contact');
            }

        }catch (\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function edit($id)
    {
        $contact = Contact::find($id);
        return Inertia::render('admin/contact/edit',[
            'contact' => $contact
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'caller_id' => 'required|numeric|unique:contacts,id,'.$id,
            'email' => 'nullable|email',
            'description' => 'nullable|string',
        ]);

        try {
            $odoo = new OdooService();

            $contact = Contact::find($id);

            $result = $odoo->updateContact($contact->odoo_contact_id, [
                'name' => $request->name,
                'email' => $request->email,
                'company_type' => 'person'
            ]);

            if($result){
                $contact->name = $request->name;
                $contact->caller_id = $request->caller_id;
                $contact->email = $request->email;
                $contact->description = $request->description;
                $contact->user_id = Auth::id();
                $contact->save();

                return to_route('admin.contacts.index')->with('success', 'Contact updated successfully');

            }else{
                return redirect()->back()->with('error', 'Failed to edit contact');
            }

        }catch (\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function destroy($id)
    {
        $contact = Contact::find($id);
        try {
            $odoo = new OdooService();
            $result = $odoo->deleteContact($contact->odoo_contact_id);
            if ($result){
                $contact->delete();

                return to_route('admin.contacts.index')->with('success', 'Contact deleted successfully');

            }else{
                return redirect()->back()->with('error', 'Failed to delete contact');
            }
        }catch (\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

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
