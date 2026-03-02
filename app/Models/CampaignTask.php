<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CampaignTask extends Model
{
    protected $fillable = [
        'campaign_id',
        'campaign_project_id',
        'title',
        'description',
        'start_date',
        'target_date',
        'status',
        'completed_at',
    ];

    /**
     * Get the campaign that owns this task
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the task member assignments
     */
    public function taskMembers(): HasMany
    {
        return $this->hasMany(CampaignTaskMember::class, 'campaign_task_id');
    }

    /**
     * Get the campaign members assigned to this task (many-to-many through pivot)
     */
    public function assignedMembers(): BelongsToMany
    {
        return $this->belongsToMany(
            CampaignMember::class,
            'campaign_task_members',
            'campaign_task_id',
            'campaign_member_id'
        );
    }

    public function remarks()
    {
        return $this->hasMany(CampaignTaskRemark::class, 'campaign_task_id');
    }

    /**
     * Get the project that this task belongs to
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(CampaignProject::class, 'campaign_project_id');
    }
}
