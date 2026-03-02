<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContributor extends Model
{
    protected $fillable = [
        'project_id',
        'campaign_id',
    ];

    /**
     * Get the project that owns the contributor.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the campaign that is contributing.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
