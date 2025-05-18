<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $guarded = [];

    protected $casts = [
        'dtmf_options' => 'array',
    ];

    public function calls()
    {
        return $this->hasMany(CampaignCall::class);
    }

    public function number_list()
    {
        return $this->belongsTo(NumberList::class, 'number_list_id');
    }

    public function voice_file()
    {
        return $this->belongsTo(VoiceFile::class);
    }

}
