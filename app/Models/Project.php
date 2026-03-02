<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'name',
        'description',
        'start_date',
        'target_date',
        'status',
    ];

    /**
     * Get the user that owns the project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campaign that owns the project.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the tasks associated with the project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    /**
     * Get the contributors associated with the project.
     */
    public function contributors(): HasMany
    {
        return $this->hasMany(ProjectContributor::class);
    }

    /**
     * Get the activities associated with the project.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class);
    }
}
