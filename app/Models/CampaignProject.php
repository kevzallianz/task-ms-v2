<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignProject extends Model
{
    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'start_date',
        'target_date',
        'priority',
        'status',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function tasks()
    {
        return $this->hasMany(CampaignTask::class, 'campaign_project_id');
    }

    /**
     * Get the activities for this campaign project
     */
    public function activities()
    {
        return $this->hasMany(CampaignProjectActivity::class, 'campaign_project_id');
    }
}
