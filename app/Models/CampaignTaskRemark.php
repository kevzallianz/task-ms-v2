<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTaskRemark extends Model
{
    protected $table = 'campaign_task_remarks';

    protected $fillable = [
        'campaign_task_id',
        'user_id',
        'remarks',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(CampaignTask::class, 'campaign_task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
