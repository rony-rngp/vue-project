<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\NumberList;
use App\Models\VoiceFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = Campaign::with(['number_list', 'voice_file'])->where('organization_id', auth()->user()->organization_id)
            ->where('user_id', auth()->id())
            ->paginate(10);
        return Inertia::render('admin/camping/CampaignList', [
            'campaigns' => $campaigns
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['number_list'] = NumberList::where('organization_id', Auth::user()->organization_id)
            ->where('user_id', Auth::user()->id)
            ->where('status', 'Completed')
            ->latest()->get();

        $data['voice_files'] = VoiceFile::where('organization_id', Auth::user()->organization_id)
            ->where('user_id', Auth::user()->id)
            ->latest()->get();

        dd($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
