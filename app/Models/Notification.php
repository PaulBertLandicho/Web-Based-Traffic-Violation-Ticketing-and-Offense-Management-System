<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['enforcer_id', 'title', 'message', 'is_read'];

    public function enforcer()
    {
        return $this->belongsTo(TrafficEnforcer::class, 'enforcer_id', 'enforcer_id');
    }
}
