<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'user_id', 'organization_id', 'number_list_id', 'voice_file_id', 'name', 'status', 'dtmf_options'
    ];

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
