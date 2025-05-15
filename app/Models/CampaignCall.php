<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignCall extends Model
{
    protected $fillable = [
        'campaign_id', 'number', 'status', 'dtmf_input', 'called_at'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
