<?php

// app/Models/TrafficAdmin.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficAdmin extends Model
{
    protected $table = 'traffic_admins';
    protected $primaryKey = 'admin_id'; // important for belongsTo
    public $timestamps = false;

    protected $fillable = [
        'admin_email',
        'admin_name', // make sure this exists
        'code',
        'otp_code',
        'otp_expires_at',
    ];
}
