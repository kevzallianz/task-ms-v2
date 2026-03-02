<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignProjectActivity extends Model
{
    protected $fillable = [
        'campaign_project_id',
        'user_id',
        'action_type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the campaign project that owns the activity.
     */
    public function campaignProject(): BelongsTo
    {
        return $this->belongsTo(CampaignProject::class);
    }

    /**
     * Get the user who performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
