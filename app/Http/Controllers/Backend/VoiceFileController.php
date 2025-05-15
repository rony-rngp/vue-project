<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\VoiceFile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VoiceFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voice_files = VoiceFile::where('organization_id', auth()->user()->organization_id)
            ->where('user_id', auth()->id())
            ->paginate(10);
        return Inertia::render('admin/camping/voice_file/index',[
            'voice_files' => $voice_files
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/camping/voice_file/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'file' => 'required|mimes:mp3,wav',
        ]);

        VoiceFile::create([
            'user_id' => auth()->id(),
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'file' => upload_file('voice_file/', $request->file('file')),
        ]);

        return redirect()->route('admin.voice-file.index')->with('success', 'Voice file uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $voice_file = VoiceFile::where('organization_id', auth()->user()->organization_id)
            ->where('user_id', auth()->id())
            ->first();
        return Inertia::render('admin/camping/voice_file/edit', [
            'voice_file' => $voice_file
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'file' => 'nullable|mimes:mp3,wav',
        ]);
        $voice_file = VoiceFile::where('organization_id', auth()->user()->organization_id)
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        $voice_file->name = $request->name;
        if($request->has('file') && $request->file != null){
            $voice_file->file = update_file('voice_file/', $voice_file->file, $request->file('file'));
        }
        $voice_file->save();

        return redirect()->route('admin.voice-file.index')->with('success', 'Voice file updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voice_file = VoiceFile::where('organization_id', auth()->user()->organization_id)
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->first();
        delete_file($voice_file->file);
        $voice_file->delete();
        return redirect()->route('admin.voice-file.index')->with('success', 'Voice file deleted successfully.');
    }
}
