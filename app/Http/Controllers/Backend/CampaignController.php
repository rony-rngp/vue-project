<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\LaunchCampaignCalls;
use App\Jobs\ProcessCampaignChunks;
use App\Jobs\StoreCall;
use App\Models\Campaign;
use App\Models\CampaignCall;
use App\Models\Number;
use App\Models\NumberList;
use App\Models\VoiceFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
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

        $campaigns->getCollection()->transform(function ($campaignList) {
            if ($campaignList->batch_id) {
                $batch = Bus::findBatch($campaignList->batch_id);
                $campaignList->progress = $batch ? $batch->progress() : 0;
            } else {
                $campaignList->progress = 0;
            }

            return $campaignList;
        });

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

        return Inertia::render('admin/camping/AddCampaign', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'voice_file_id' => 'required|exists:voice_files,id',
            'number_list_id' => 'required|exists:number_lists,id',
        ]);

        $campaign = Campaign::create([
            'user_id' => auth()->id(),
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'voice_file_id' => $request->voice_file_id,
            'number_list_id' => $request->number_list_id,
            'process_status' => 'Processing',
            'dtmf_options' => $request->dtmf_options,
        ]);

        ProcessCampaignChunks::dispatch($campaign->id, $request->number_list_id);

        /*$batchJobs = [];

        $numbers = Number::where('number_list_id', $request->number_list_id)->pluck('phone_number')->toArray();
        $campaign->update(['total_numbers' => count($numbers)]);

        $chunks = array_chunk($numbers, 1000);

        foreach ($chunks as $chunk){
            $batchJobs[] = new StoreCall($campaign->id, $chunk);
        }

        $batch = Bus::batch($batchJobs)  // Pass the array of jobs to the batch
            ->then(function () use ($campaign) {
                // This callback will be triggered when all jobs are completed successfully
                $campaign->update(['process_status' => 'Processed']);
            })
            ->catch(function () use ($campaign) {
                // This callback will be triggered if any job in the batch fails
                $campaign->update(['process_status' => 'Failed']);
            })
            ->finally(function () use ($campaign) {
                // Optional: You can add any final action here if needed, like logging.
            })
            ->dispatch();  // Dispatch the batch for processing

        $campaign->update(['batch_id' => $batch->id]);*/

        return to_route('admin.campaigns.index')->with('success', 'Campaign added successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $campaign = Campaign::with(['number_list', 'voice_file'])->where('organization_id', auth()->user()->organization_id)
            ->where('user_id', auth()->id())->where('id', $id)->first();
        $campaign_calls = CampaignCall::where('campaign_id', $campaign->id)->paginate(100);
        return Inertia::render('admin/camping/CampaingDetails', [
            'campaign' => $campaign,
            'campaign_calls' => $campaign_calls,
        ]);
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
        $campaign = Campaign::where('user_id', Auth::user()->id)
            ->where('organization_id', auth()->user()->organization_id)
            ->where('id', $id)->first();
        CampaignCall::where('campaign_id', $campaign->id)->delete();
        $campaign->delete();
        return to_route('admin.campaigns.index')->with('success', 'Campaign deleted successfully');
    }

    public function launch($id)
    {
        $campaign = Campaign::where('user_id', Auth::user()->id)
            ->where('id', $id)
            ->where('organization_id', auth()->user()->organization_id)
            ->first();
        // Ensure the campaign is running
        if ($campaign->status == 'running'){
            return redirect()->back()->with('error', 'Campaign already launched');
        }
        $campaign->update(['status' => 'running']);

        // Dispatch the queue to process the calls
        LaunchCampaignCalls::dispatch($campaign);

        return back()->with('success', 'Campaign launched');
    }

    public function pause(Campaign $campaign)
    {
        $campaign->update(['status' => 'paused']);
        return back()->with('success', 'Campaign paused');
    }

    public function resume(Campaign $campaign)
    {
        $campaign->update(['status' => 'running']);
        LaunchCampaignCalls::dispatch($campaign);
        return back()->with('success', 'Campaign resumed');
    }


}
