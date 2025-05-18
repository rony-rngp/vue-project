<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\StoreNumber;
use App\Models\Number;
use App\Models\NumberList;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Throwable;


class NumberListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $numbers = NumberList::withCount('numbers')
            ->where('organization_id', Auth::user()->organization_id)
            ->where('user_id', Auth::user()->id)->latest()->paginate(10);

        // For each number list, check if there is an active batch
        $numbers->getCollection()->transform(function ($numberList) {
            if ($numberList->batch_id) {
                $batch = Bus::findBatch($numberList->batch_id);
                $numberList->progress = $batch ? $batch->progress() : 0;
                //$numberList->status = $batch && $batch->finished() ? 'Completed' : 'Processing';
            } else {
                $numberList->progress = 0;
                //$numberList->status = 'Not Started';
            }

            return $numberList;
        });
        return Inertia::render('admin/camping/numberList/index',[
            'number_list' => $numbers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        // Create a new NumberList entry
        $numberList = NumberList::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
            'organization_id' => Auth::user()->organization_id,
            'status' => 'Processing',
        ]);


        $file = $request->file('csv_file');

        // Open the file and process the contents
        $path = $file->getRealPath();

        $data = file($path, FILE_IGNORE_NEW_LINES);
        unset($data[0]);
        $chunks = array_chunk($data, 1000);

        $batchJobs = [];

        foreach ($chunks as $chunk){
            $batchJobs[] = new StoreNumber($chunk, $numberList->id);
        }

        $batch = Bus::batch($batchJobs)  // Pass the array of jobs to the batch
        ->then(function () use ($numberList) {
            // This callback will be triggered when all jobs are completed successfully
            $numberList->update(['status' => 'Completed']);
        })
            ->catch(function () use ($numberList) {
                // This callback will be triggered if any job in the batch fails
                $numberList->update(['status' => 'Failed']);
            })
            ->finally(function () use ($numberList) {
                // Optional: You can add any final action here if needed, like logging.
                //Log::info("Batch processing completed for NumberList ID: {$numberList->id}");
            })
            ->dispatch();  // Dispatch the batch for processing

        $numberList->update(['batch_id' => $batch->id]);

        // Return response
        return redirect()->route('admin.number-list.index')->with('success', 'Upload started. Processing in background.');

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
        $number_list = NumberList::where('user_id', Auth::user()->id)->where('id', $id)->first();
        Number::where('number_list_id', $number_list->id)->delete();
        $number_list->delete();
        return redirect()->route('admin.number-list.index')->with('success', 'Number list deleted successfully');
    }
}
