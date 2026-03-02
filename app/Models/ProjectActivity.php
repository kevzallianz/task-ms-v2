<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectActivity extends Model
{
    protected $fillable = [
        'project_id',
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
     * Get the project that owns the activity.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
