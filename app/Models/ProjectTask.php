<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTask extends Model
{
    protected $fillable = [
        'project_id',
        'assigned_campaign_id',
        'assigned_campaign_member_id',
        'title',
        'description',
        'start_date',
        'target_date',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'assigned_campaign_id');
    }

    public function campaignMember(): BelongsTo
    {
        return $this->belongsTo(CampaignMember::class, 'assigned_campaign_member_id');
    }

    public function remarks()
    {
        return $this->hasMany(ProjectRemarks::class, 'project_task_id');
    }
}
