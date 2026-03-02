<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the tasks for this campaign
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(CampaignTask::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the projects for this campaign
     */
    public function projects(): HasMany
    {
        return $this->hasMany(CampaignProject::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the campaign members (pivot model)
     */
    public function campaignMembers(): HasMany
    {
        return $this->hasMany(CampaignMember::class);
    }

    /**
     * Get the members of this campaign through CampaignMember
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'campaign_members')
            ->withPivot('id', 'access_level')
            ->withTimestamps();
    }

    /**
     * Get campaign members with user details via relationship
     */
    public function getMembersWithUsers()
    {
        return $this->campaignMembers()
            ->with('user')
            ->get()
            ->pluck('user');
    }
}
