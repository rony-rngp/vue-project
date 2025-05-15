<?php

namespace App\Jobs;

use App\Models\Number;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Batchable; // Import the Batchable trait


class StoreNumber implements ShouldQueue
{
    use Queueable, Batchable;

    public $deta, $number_list_id;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $number_list_id)
    {
        $this->deta = $data;
        $this->number_list_id = $number_list_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->deta as $num_data){
            $number = new Number();
            $number->number_list_id = $this->number_list_id;
            $number->phone_number = $num_data;
            $number->save();
        }
    }
}
