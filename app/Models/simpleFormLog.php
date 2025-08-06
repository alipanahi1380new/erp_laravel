<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class simpleFormLog extends Model
{
    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'user_id',
        'action',
        'old_data',
        'new_data',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function loggable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
