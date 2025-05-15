<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoiceFile extends Model
{
    protected $fillable = [
        'user_id',
        'organization_id',
        'name',
        'file',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
