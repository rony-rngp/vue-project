<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected static function createTicketNo(){
        $start = 1001;
        $number = $start;
        while (static::where('ticket_no', $number)->exists()) {
            $number++;
        }
        return $number;
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

}
