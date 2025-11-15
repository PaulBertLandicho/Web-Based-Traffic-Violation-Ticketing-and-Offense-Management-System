<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnforcerViolation extends Model
{
    protected $table = 'enforcer_violations';

    protected $fillable = [
        'enforcer_id',
        'violation_type',
        'details',
        'remarks',
        'penalty_amount',
        'status',
        'settled_at',
        'date_issued'
    ];

    // Relationship: Violation belongs to traffic enforcer
    public function enforcer()
    {
        return $this->belongsTo(TrafficEnforcer::class, 'enforcer_id', 'enforcer_id');
    }
}
