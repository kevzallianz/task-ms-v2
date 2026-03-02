<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMember extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'access_level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
    public function tasks()
    {
        return $this->hasMany(CampaignTask::class, 'assigned_campaign_member_id');
    }
}
