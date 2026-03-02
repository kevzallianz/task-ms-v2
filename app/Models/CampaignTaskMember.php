<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTaskMember extends Model
{
    protected $table = 'campaign_task_members';

    protected $fillable = [
        'campaign_task_id',
        'campaign_member_id',
    ];

    /**
     * Get the campaign task
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(CampaignTask::class, 'campaign_task_id');
    }

    /**
     * Get the campaign member
     */
    public function campaignMember(): BelongsTo
    {
        return $this->belongsTo(CampaignMember::class, 'campaign_member_id');
    }
}
