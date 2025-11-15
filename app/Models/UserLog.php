<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'enforcer_id',
        'action',
        'details',
        'ip_address',
        'is_read'
    ];

    public function enforcer()
    {
        return $this->belongsTo(TrafficEnforcer::class, 'enforcer_id', 'enforcer_id');
    }
}
