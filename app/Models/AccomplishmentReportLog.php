<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccomplishmentReportLog extends Model
{
    protected $fillable = [
        'user_id',
        'leader_email',
        'accomplishments',
        'status',
        'error_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
