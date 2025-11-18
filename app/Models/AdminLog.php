<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $table = 'admin_logs';
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'action',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $dates = ['created_at']; // <-- add this

    public function admin()
    {
        return $this->belongsTo(\App\Models\TrafficAdmin::class, 'admin_id', 'admin_id');
    }
}
