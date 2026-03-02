<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignProject extends Model
{
    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'start_date',
        'target_date',
        'status',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the activities for this campaign project
     */
    public function activities()
    {
        return $this->hasMany(CampaignProjectActivity::class, 'campaign_project_id');
    }
}
